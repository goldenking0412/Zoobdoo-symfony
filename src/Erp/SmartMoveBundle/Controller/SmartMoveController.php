<?php

namespace Erp\SmartMoveBundle\Controller;

use Erp\CoreBundle\Controller\BaseController;
use Erp\CoreBundle\EmailNotification\EmailNotificationFactory;
use Erp\PaymentBundle\Entity\StripeCustomer;
use Erp\SmartMoveBundle\Entity\SmartMoveRenter;
use Erp\SmartMoveBundle\Form\Type\SmartMoveEmailFormType;
use Erp\SmartMoveBundle\Form\Type\SmartMoveExamFormType;
use Erp\SmartMoveBundle\Form\Type\SmartMoveGetReportFormType;
use Erp\SmartMoveBundle\Form\Type\SmartMovePersonalFormType;
use Erp\StripeBundle\Entity\Transaction;
use Erp\StripeBundle\Helper\ApiHelper;
use Erp\UserBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SmartMoveController extends BaseController {

    const EMAIL = 'finish.renter2@mailinator.com';

    /**
     * Background Check/Credit Check widget
     *
     * @Security("is_granted('ROLE_MANAGER') or is_granted('ROLE_LANDLORD')")
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function backgroundCreditCheckWidgetAction(Request $request) {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('fos_user_security_login');
        }

        $form = $this->createCheckForm($user);
        if ($request->getMethod() === 'POST') {
            $teml = 'Background Check/Credit Check: ';
            $errors = null;
            $form->handleRequest($request);

            if ($form->isValid() && !$user->isReadOnlyUser()) {
                /** @var $smartMoveRenter SmartMoveRenter */
                $smartMoveRenter = $this->addSMRenter($form->getData());
                if ($smartMoveRenter) {
                    $this->sendEmailToCheckingUser($smartMoveRenter);

                    $msg = 'Message has been sent to an email you have entered with instructions for Tenant.';
                    $this->get('session')->getFlashBag()->add('alert_ok', $teml . $msg);
                } else {
                    $errors = 'The user with this email is already in identity verification process.';
                }
            } else {
                $emailErr = $form->get('email')->getErrors();
                if (isset($emailErr[0])) {
                    $errors = $emailErr[0]->getMessageTemplate();
                }
            }

            if ($errors) {
                $this->get('session')->getFlashBag()->add('alert_error', $teml . $errors);
            }

            return $this->redirectToRoute('erp_smart_move_check_page');
        }


        return $this->render('ErpSmartMoveBundle:Widgets:background-credit-check.html.twig', [
                    'user' => $user,
                    'form' => $form->createView(),
                    'smartMoveEnable' => $this->get('erp.core.fee.service')->getSmartMoveEnable(),
        ]);
    }

    /**
     * Page with Background checks form
     *
     * @Security("is_granted('ROLE_MANAGER') or is_granted('ROLE_LANDLORD')")
     * @return RedirectResponse|Response
     */
    public function backgroundChecksAction() {
        /** @var $user \Erp\UserBundle\Entity\User */
        $currentUser = $this->getUser();

        if (!$currentUser) {
            return $this->redirectToRoute('fos_user_security_login');
        }

        $menu = $this->get('erp.site.left_menu_service')->createLeftMenu(
            $currentUser,
            (new User())
            ->setId(0)
            ->setFirstName('Applicants')
            ->addRole(User::ROLE_ANONYMOUS)
        );

        return $this->render('ErpSmartMoveBundle:SmartMove:background-checks.html.twig', [
            'user' => $currentUser,
            'menu' => $menu,
            'role_tenant' => User::ROLE_TENANT,
            'role_manager' => User::ROLE_MANAGER,
            'role_anonymous' => User::ROLE_ANONYMOUS
        ]);
    }

    /**
     * SmartMove Personal form
     *
     * @param Request $request
     * @param string  $token
     *
     * @return RedirectResponse|Response
     */
    public function personalFormAction(Request $request, $token) {
        $user = $this->getUser();
        $smartMoveRenter = $this->em->getRepository('ErpSmartMoveBundle:SmartMoveRenter')
                ->findOneBy(['personalToken' => $token, 'isPersonalComleted' => false]);

        $isRoleAccess = false;
        if ($user) {
            $isRoleAccess = $user->hasRole(User::ROLE_MANAGER) || $user->hasRole(User::ROLE_SUPER_ADMIN) || $user->hasRole(User::ROLE_ADMIN);
        }
        if (!$smartMoveRenter || $isRoleAccess) {
            throw $this->createNotFoundException();
        }

        $form = $this->createPersonalForm($smartMoveRenter);
        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            $form = $this->checkFormDateValidation($form);

            if ($form->isValid()) {
                $data = $this->prepareFormParams($form);
                $this->em->persist($smartMoveRenter->setInfo($data));

                $examToken = $this->createSmartMoveRenter($smartMoveRenter);

                if ($examToken) {
                    return $this->redirectToRoute('erp_smart_move_exam_form', ['token' => $examToken]);
                }
            }
        }

        return $this->render('ErpSmartMoveBundle:SmartMove:index.html.twig', ['user' => $this->getUser(), 'form' => $form->createView(), 'isPersonalForm' => true]
        );
    }

    /**
     * SmartMove Exam form
     *
     * @param Request $request
     * @param string  $token
     *
     * @return Response
     */
    public function examFormAction(Request $request, $token) {
        $user = $this->getUser();
        $smartMoveRenter = $this->em->getRepository('ErpSmartMoveBundle:SmartMoveRenter')->findOneBy(
                ['examToken' => $token, 'isPersonalComleted' => true]
        );

        $isManager = $user && $user->hasRole(User::ROLE_MANAGER);
        if (!$smartMoveRenter || $isManager || $smartMoveRenter->getIsExamComleted()) {
            throw $this->createNotFoundException();
        }

        $smartmoveService = $this->get('erp.smartmove.smartmove_service');
        $examForm = $isAnswered = $isError = false;
        if ($request->getMethod() === 'GET') {
            $examResponse = $smartmoveService->retrieveExamRenter($smartMoveRenter);
            if ($examResponse['status']) {
                $form = $this->createExamForm($smartMoveRenter);
                $examForm = $form->createView();
            } else {
                $isError = true;
            }
        }

        if ($request->getMethod() === 'POST') {
            $form = $this->createExamForm($smartMoveRenter);
            $form->handleRequest($request);

            if ($form->isValid()) {
                $smartMoveRenter = $this->prepareExamForm($form, $smartMoveRenter);
                $evaluateResponse = $smartmoveService->evaluateExamRenter($smartMoveRenter);

                if ($evaluateResponse['status'] && $smartMoveRenter->getIsExamComleted()) {
                    $isAnswered = true;
                } else {
                    $examResponse = $smartmoveService->retrieveExamRenter($smartMoveRenter);
                    if ($examResponse['status']) {
                        $form = $this->createExamForm($smartMoveRenter);
                        $examForm = $form->createView();
                    } else {
                        $isError = true;
                    }
                }
            }
        }

        return $this->render('ErpSmartMoveBundle:SmartMove:index.html.twig', [
                    'user' => $this->getUser(),
                    'form' => $examForm,
                    'isPersonalForm' => false,
                    'isAnswered' => $isAnswered,
                    'isError' => $isError
        ]);
    }

    /**
     * Get report by renter
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getReportsAction(Request $request) {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('fos_user_security_login');
        }

        $template = 'ErpSmartMoveBundle:SmartMove:get-reports-form.html.twig';
        if (!$user->hasRole(User::ROLE_MANAGER)) {
            $env = $this->container->get('kernel')->getEnvironment();
            if ($env == 'dev') {
                $response = $this->render($template, array('exception' => $this->createNotFoundException('No info to show: you are not a manager')));
            } else {
                throw $this->createNotFoundException();
            }
        } else {
            $smRenters = $this->em->getRepository('ErpSmartMoveBundle:SmartMoveRenter')->findBy(
                    ['manager' => $user, 'isExamComleted' => true]
            );
            $form = $this->createGetReportForm($smRenters);

            $response = $this->render($template, array('form' => $form->createView(), 'user' => $user));

            if ($request->getMethod() === 'POST') {
                $form->handleRequest($request);

                if ($form->isValid() && !$user->isReadOnlyUser()) {
                    $formData = $form->getData();
                    foreach ($smRenters as $renter) {
                        if ($renter->getId() == $formData['smRenters']) {
                            $smartMoveRenter = $renter;
                            break;
                        }
                    }

                    $response = $this->getReportPDFResponse($smartMoveRenter);
                    if (!$response) {
                        $response = $this->redirectToRoute('erp_user_profile_dashboard');
                    }
                }
            }
        }

        return $response;
    }

    /**
     * Check is Paid by SmartMove Report
     *
     * @param $smRenterId
     *
     * @return JsonResponse
     */
    public function checkIsPaidAction($smRenterId) {
        if (!$smRenterId) {
            throw $this->createNotFoundException();
        }

        $smRenter = $this->em->getRepository('ErpSmartMoveBundle:SmartMoveRenter')->find($smRenterId);
        if (!$smRenter) {
            throw $this->createNotFoundException();
        }

        return new JsonResponse(['status' => $smRenter->getIsPayed()]);
    }

    /**
     * @param Request $request
     * @param $smRenterId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Erp\SmartMoveBundle\Exceptions\SmartMoveManagerException
     */
    public function payReportAction(Request $request, $smRenterId) {
        /** @var $user User */
        $user = $this->getUser();
        //TODO Add security role
        $customer = $user->getStripeCustomer();
        //TODO use ParamConverter?
        $smRenter = $this->em->getRepository('ErpSmartMoveBundle:SmartMoveRenter')->find($smRenterId);

        if (!$smRenter || !$customer) {
            throw $this->createNotFoundException();
        }

        $amount = $this->get('erp.core.fee.service')->getTenantScreeningFee();

        if ($request->getMethod() === Request::METHOD_POST) {
            if (!$smRenter->getIsPayed()) {
                /** @var Erp\PaymentBundle\Service\Service $manager */
                $stripeManager = $this->get('erp.payment.service');
                $response = $stripeManager->makeSinglePayment(null, $amount, array('internalType' => Transaction::INTERNAL_TYPE_TENANT_SCREENING), null, $customer);

                if (!$response->isSuccess()) {
                    $this->addFlash('alert_error', json_encode($response->getErrorMessage()));
                } else {
                    $this->em->persist($smRenter->setIsPayed(true));
                    $this->em->flush();

                    $this->get('erp.smartmove.smartmove_service')->generateRenterReports($smRenter);

                    $msg = 'Please wait for about 30 minutes for your reports to be generated, then select Tenant\'s
                    email and press GET REPORT button again.';
                    $this->get('session')->getFlashBag()->add('alert_ok', $msg);
                }
            }

            return $this->redirectToRoute('erp_user_profile_dashboard');
        }

        return $this->render('ErpCoreBundle:crossBlocks:general-confirmation-popup.html.twig', [
                    'askMsg' => 'You will be charged $' . $amount . ' for this feature. Do you want to proceed?',
                    'actionBtn' => 'Yes',
                    'actionUrl' => $this->generateUrl('erp_smart_move_smart_move_pay_report', ['smRenterId' => $smRenter->getId()])
        ]);
    }

    /**
     * Create Background/Credit check form
     *
     * @param User $user
     *
     * @return \Symfony\Component\Form\Form
     */
    private function createCheckForm(User $user) {
        $smRenter = new SmartMoveRenter();
        $smRenter->setManager($user);

        $formOptions = ['action' => $this->generateUrl('erp_smart_move_check'), 'method' => 'POST'];
        $form = $this->createForm(SmartMoveEmailFormType::class, $smRenter, $formOptions);

        return $form;
    }

    /**
     * Create Get Report form
     *
     * @param array $smRenters
     *
     * @return \Symfony\Component\Form\Form
     */
    private function createGetReportForm(array $smRenters = []) {
        $formOptions = ['action' => $this->generateUrl('erp_smart_move_get_reports'), 'method' => 'POST'];
        $form = $this->createForm(new SmartMoveGetReportFormType($smRenters), null, $formOptions);

        return $form;
    }

    /**
     * Create SmartMove Personal Form
     *
     * @param SmartMoveRenter $smartMoveRenter
     *
     * @return \Symfony\Component\Form\Form
     */
    private function createPersonalForm(SmartMoveRenter $smartMoveRenter) {
        $formOptions = [
            'action' => $this->generateUrl(
                    'erp_smart_move_personal_form', ['token' => $smartMoveRenter->getPersonalToken()]
            ),
            'method' => 'POST'
        ];
        $form = $this->createForm(
                new SmartMovePersonalFormType($this->get('erp.core.location'), $smartMoveRenter->getEmail()), null, $formOptions
        );

        return $form;
    }

    /**
     * Create SmartMove Exam Form
     *
     * @param SmartMoveRenter $smartMoveRenter
     *
     * @return \Symfony\Component\Form\Form
     */
    private function createExamForm(SmartMoveRenter $smartMoveRenter) {
        $formOptions = [
            'action' => $this->generateUrl(
                    'erp_smart_move_exam_form', ['token' => $smartMoveRenter->getExamToken()]
            ),
            'method' => 'POST'
        ];
        $form = $this->createForm(new SmartMoveExamFormType($smartMoveRenter->getExams()), null, $formOptions);

        return $form;
    }

    /**
     * Create and accept new SmartMove Renter
     *
     * @param SmartMoveRenter $smartMoveRenter
     *
     * @return null|string
     */
    private function createSmartMoveRenter(SmartMoveRenter $smartMoveRenter) {
        $token = null;
        $smartmoveService = $this->get('erp.smartmove.smartmove_service');
        $smartMoveRenterResponse['status'] = true;
        $smartMoveRenterResponse['data'] = $smartMoveRenter;

        if (!$smartMoveRenter->getIsAddedAsApplicant()) {
            $smartMoveRenterResponse = $smartmoveService->newApplicantSmartMoveRequest($smartMoveRenter);
        }

        if ($smartMoveRenterResponse['status']) {
            $newRenterResponse = $smartmoveService->createNewRenter($smartMoveRenterResponse['data']);

            if ($newRenterResponse['status']) {
                $statusResponse = $smartmoveService->acceptApplicationByRenter($newRenterResponse['data']);

                if ($statusResponse['status']) {
                    $smartMoveRenter = $statusResponse['data'];

                    $examToken = $this->get('fos_user.util.token_generator')->generateToken();
                    $smartMoveRenter->setExamToken($examToken)->setIsPersonalComleted(true);
                    $this->em->persist($smartMoveRenter);
                    $this->em->flush();

                    $this->sendEmailToCheckingUser($smartMoveRenter, true);

                    $token = $examToken;
                }
            }
        }

        return $token;
    }

    /**
     * Add SmartMove Renter
     *
     * @param SmartMoveRenter $smartMoveRenter
     *
     * @return SmartMoveRenter|null
     */
    private function addSMRenter(SmartMoveRenter $smartMoveRenter) {
        $existSMRenter = $this->em->getRepository('ErpSmartMoveBundle:SmartMoveRenter')->findOneBy(
                ['email' => $smartMoveRenter->getEmail()]
        );

        if ($existSMRenter) {
            $smartMoveRenter = $existSMRenter;
            if ($existSMRenter->getIsPersonalComleted()) {
                $smartMoveRenter = null;
            }
        }

        if ($smartMoveRenter) {
            $smartMoveRenter->setPersonalToken($this->get('fos_user.util.token_generator')->generateToken())
                    ->setIsPersonalComleted(false);
            $this->em->persist($smartMoveRenter);
            $this->em->flush();
        }

        return $smartMoveRenter;
    }

    /**
     * Check personal form dateOfBirth validation
     *
     * @param Form $form
     *
     * @return Form
     */
    private function checkFormDateValidation(Form $form) {
        $data = $form->getData();
        $dateOfBirth = new \DateTime($data['DateOfBirth']);
        $today = new \DateTime();

        $dateDiff = $today->diff($dateOfBirth);
        $yearsCnt = (int) $dateDiff->format('%y');

        if ($yearsCnt > 125 || $yearsCnt < 18 || ($dateOfBirth > $today && $yearsCnt > 18)) {
            $form->get('DateOfBirth')->addError(new FormError('Age must be 18 or over and under 125.'));
        }

        return $form;
    }

    /**
     * Prepare form params
     *
     * @param Form $form
     *
     * @return string
     */
    private function prepareFormParams(Form $form) {
        $data = $form->getData();
        if (!$data['MiddleName']) {
            unset($data['MiddleName']);
        }
        if (!$data['StreetAddressLineTwo']) {
            unset($data['StreetAddressLineTwo']);
        }

        if (!$data['HomePhoneNumber']) {
            unset($data['HomePhoneNumber']);
        }

        if (!$data['OtherIncome']) {
            unset($data['OtherIncome']);
        }

        if (!$data['OtherIncomeFrequency']) {
            unset($data['OtherIncomeFrequency']);
        }

        if (!$data['AssetValue']) {
            unset($data['AssetValue']);
        }

        $data = json_encode($data);

        return $data;
    }

    /**
     * Set Renter exam answers
     *
     * @param Form            $form
     * @param SmartMoveRenter $smartMoveRenter
     *
     * @return SmartMoveRenter
     */
    private function prepareExamForm(Form $form, SmartMoveRenter $smartMoveRenter) {
        $data = $form->getData();
        $exams = json_decode($smartMoveRenter->getExams(), true);

        if (isset($exams['Questions'])) {
            foreach ($exams['Questions'] as $key => $question) {
                $qId = $exams['Questions'][$key]['Id'];

                foreach (array_keys($data) as $aId) {
                    if ($aId == $qId) {
                        $exams['Questions'][$key]['SelectedAnswerId'] = $data[$aId];
                        break;
                    }
                }
            }
        }
        $this->em->persist($smartMoveRenter->setExams(json_encode($exams)));
        $this->em->flush();

        return $smartMoveRenter;
    }

    /**
     * Sent email to user to check via SmartMove
     *
     * @param SmartMoveRenter $smartMoveRenter
     * @param bool            $isToExam
     *
     * @return bool
     */
    private function sendEmailToCheckingUser(SmartMoveRenter $smartMoveRenter, $isToExam = false) {
        /** @var User $user */
        $user = $this->getUser();
        $preSubject = $user->getSubjectForEmail();
        $emailType = EmailNotificationFactory::TYPE_SM_CHECK_USER;
        $token = $smartMoveRenter->getPersonalToken();
        $title = $preSubject . ' - Tenant Screening';
        $text = 'Your Manager is going to perform tenant screening on your identity.';
        $url = 'erp_smart_move_personal_form';

        if ($isToExam) {
            $token = $smartMoveRenter->getExamToken();
            $url = 'erp_smart_move_exam_form';
            $text = 'Please pass identity verification exam for tenant screening service.';
            $title = $preSubject . ' - Tenant Screening Exam';
        }

        $emailParams = [
            'sendTo' => $smartMoveRenter->getEmail(),
            'url' => $this->generateUrl($url, ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL),
            'text' => $text,
            'title' => $title,
            'preSubject' => $preSubject,
            'mailFromTitle' => $user->getFromForEmail(),
        ];

        $sentStatus = $this->container->get('erp.core.email_notification.service')->sendEmail($emailType, $emailParams);

        return $sentStatus;
    }

    /**
     * Export Renter report to pdf
     *
     * @param SmartMoveRenter $smartMoveRenter
     *
     * @return mixed
     */
    private function getReportPDFResponse(SmartMoveRenter $smartMoveRenter) {
        $response = null;
        if ($smartMoveRenter->getReports()) {
            $reportResponse['status'] = true;
            $reportResponse['data'] = $smartMoveRenter;
        } else {
            $reportResponse = $this->get('erp.smartmove.smartmove_service')->getReports($smartMoveRenter);
        }

        if ($reportResponse['status']) {
            $smartMoveRenter = $reportResponse['data'];

            if (!$smartMoveRenter->getReports()) {
                $this->get('session')->getFlashBag()->add(
                        'alert_error', 'Background Check/Credit Check: The report has not been generated yet. Please try again later.'
                );
            } else {
                $html = $this->renderView(
                        'ErpSmartMoveBundle:SmartMove:export-pdf.html.twig', ['reports' => json_decode($smartMoveRenter->getReports(), true)]
                );
                /** @var $dompdf \Slik\DompdfBundle\Wrapper\DompdfWrapper */
                $dompdf = $this->get('slik_dompdf');
                $dompdf->getpdf($html);
                $dompdf->stream('Report.pdf', ['Attachment' => '0']);
                $response = $dompdf->output();
            }
        }

        return $response;
    }

}
