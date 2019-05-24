<?php

namespace Erp\UserBundle\Controller;

use Erp\UserBundle\Entity\ProRequest;
use Erp\UserBundle\Entity\User;
use Erp\UserBundle\Form\Type\AddressDetailsFormType;
use Erp\UserBundle\Form\Type\AskProFormType;
use Erp\UserBundle\Form\Type\DashboardServiceRequestFormType;
use Erp\UserBundle\Form\Type\EmailOptionsFormType;
use Erp\UserBundle\Form\Type\ManagerDetailsFormType;
use Erp\UserBundle\Form\Type\TenantContactInfoFormType;
use Erp\UserBundle\Form\Type\TenantDetailsFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Erp\CoreBundle\Controller\BaseController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Erp\CoreBundle\EmailNotification\EmailNotificationFactory;
use Symfony\Component\Validator\Constraints\Email as EmailConstraint;
use Symfony\Component\Form\FormError;
use Erp\UserBundle\Form\Type\MessagesFormType;

/**
 * Class ProfileController
 *
 * @package Erp\UserBundle\Controller
 */
class ProfileController extends BaseController {

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * This used instead of __construct as Symfony2 controllers don't support constructors by default
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null) {
        parent::setContainer($container);
    }

    /**
     * Dashboard
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return Response
     */
    public function showAction() {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->getUser();

        if ($user->hasRole(User::ROLE_MANAGER)) {
            $templateName = 'Manager';
        } elseif ($user->hasRole(User::ROLE_TENANT)) {
            $templateName = 'Tenant';
        } elseif ($user->hasRole(User::ROLE_LANDLORD)) {
            $templateName = 'Landlord';
        } else {
            throw new \RuntimeException('Please, specify profile template for role.');
        }

        return $this->render('ErpUserBundle:Profile/' . $templateName . ':dashboard.html.twig', ['user' => $user]);
    }

    /**
     * Render widget message fot tenant
     *
     * @return Response
     */
    public function renderWidgetMessagesAction() {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->getUser();

        if (!$user->hasRole(User::ROLE_TENANT)) {
            throw new NotFoundHttpException('Not permissions');
        }

        $tenantProperty = $user->getTenantProperty();

        if (!$tenantProperty) {
            throw new NotFoundHttpException('Tenant not relations with manager');
        }

        $form = $this->createFormMessageForm($tenantProperty->getUser());

        return $this->render('ErpUserBundle:Profile/Tenant/widgets:dashboard-messages.html.twig', [
                    'form' => $form->createView()
        ]);
    }

    /**
     * Render widget service request
     *
     * @return Response
     */
    public function renderWidgetServiceRequestAction() {
        $form = $this->createFormServiceRequest();

        return $this->render('ErpUserBundle:Profile/Tenant/widgets:dashboard-service-request.html.twig', [
                    'form' => $form->createView()
        ]);
    }

    /**
     * Details form
     *
     * @param Request $request
     *
     * @return Response
     */
    public function detailsAction(Request $request) {
        $user = $this->getUser();
        $type = $user->hasRole(User::ROLE_MANAGER) ? new ManagerDetailsFormType() : new TenantDetailsFormType();
        $action = $this->generateUrl('erp_user_details');

        $formOptions = ['action' => $action, 'method' => 'POST'];
        $form = $this->createForm($type, $user, $formOptions);

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            $flag = 0;

            if ($form->isValid()) {
                $plainPassword = $form->get('plainPassword')->getData();
                $oldPassword = $form->get('oldPassword')->getData();

                if ($plainPassword && $oldPassword) {
                    $encoder = $this->container->get('security.password_encoder');
                    $oldPasswordEncoded = $encoder->encodePassword($user, $oldPassword);
                    $newPasswordEncoded = $encoder->encodePassword($user, $plainPassword);

                    if ($oldPasswordEncoded === $user->getPassword()) {
                        $user->setPassword($newPasswordEncoded);
                    } else {
                        $form->get('oldPassword')->addError(new FormError('Wrong value for your current password'));
                        $flag = 1;
                    }
                }

                if ($plainPassword and ! $oldPassword) {
                    $form->get('oldPassword')->addError(new FormError('Please enter your current password'));
                    $flag = 1;
                }

                if (!$flag) {
                    $this->em->persist($user);
                    $this->em->flush();

                    $teml = 'Account Login Details: ';
                    $this->get('session')->getFlashBag()->add(
                            'alert_ok', $teml . 'has been successfully saved'
                    );

                    return $this->redirectToRoute('erp_user_profile_dashboard');
                }
            }
        }

        return $this->render(
                        'ErpUserBundle:Profile:details.html.twig', ['form' => $form->createView(), 'user' => $user]
        );
    }

    /**
     * Address details form
     *
     * @param Request $request
     *
     * @return Response
     */
    public function addressDetailsAction(Request $request) {
        $user = $this->getUser();

        $type = new AddressDetailsFormType($request, $this->get('erp.core.location')->getStates());
        $action = $this->generateUrl('erp_user_address_details');

        $formOptions = ['action' => $action, 'method' => 'POST'];

        $form = $this->createForm($type, $user, $formOptions);

        $teml = 'Address Details: ';

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->em->persist($user);
                $this->em->flush();

                $this->get('session')->getFlashBag()->add(
                        'alert_ok', $teml . 'has been successfully saved'
                );

                return $this->redirectToRoute('erp_user_profile_dashboard');
            }
        }

        return $this->render('ErpUserBundle:Profile:address-details.html.twig', [
                    'form' => $form->createView(),
                    'user' => $user
        ]);
    }

    /**
     * Address details form
     *
     * @param Request $request
     *
     * @return Response
     */
    public function contactInfoAction(Request $request) {
        $user = $this->getUser();

        $type = new TenantContactInfoFormType($request, $this->get('erp.core.location')->getStates());
        $action = $this->generateUrl('erp_user_contact_info');

        $formOptions = ['action' => $action, 'method' => 'POST'];

        $form = $this->createForm($type, $user, $formOptions);

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->em->persist($user);
                $this->em->flush();

                $this->addFlash('alert_ok', 'Contact info: has been successfully saved');

                return $this->redirectToRoute('erp_user_contact_info');
            }
        }

        return $this->render('ErpUserBundle:Profile:contact-info.html.twig', [
                    'form' => $form->createView(),
                    'user' => $user
        ]);
    }

    /**
     * Get email options form
     *
     * @param Request $request
     *
     * @return Response
     */
    public function emailOptionsAction(Request $request) {
        $user = $this->getUser();

        $type = new EmailOptionsFormType();
        $action = $this->generateUrl('erp_user_email_options');

        $formOptions = ['action' => $action, 'method' => 'POST'];
        $form = $this->createForm($type, $user, $formOptions);

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            $flag = 0;

            $formData = $request->request->get($form->getName());

            if (!empty($formData['secondEmail'])) {
                if ($form->isValid()) {
                    $secondEmail = $formData['secondEmail'];
                    $existEmail = $this->em->getRepository('ErpUserBundle:User')->findOneBy(['email' => $secondEmail]);

                    if ($existEmail and $user->getSecondEmail() == $existEmail) {
                        $form->get('secondEmail')->addError(new FormError('Email is already in use'));
                        $flag = 1;
                    } else {
                        $user->setSecondEmail($secondEmail);
                    }
                } else {
                    $flag = 1;
                }
            } else {
                $user->setSecondEmail('');
            }

            if (!$flag) {
                $this->em->persist($user);
                $this->em->flush();

                $teml = 'Email Options: ';

                $this->get('session')->getFlashBag()->add(
                        'alert_ok', $teml . 'has been successfully saved'
                );

                return $this->redirectToRoute('erp_user_profile_dashboard');
            }
        }

        return $this->render('ErpUserBundle:Profile:email-options.html.twig', [
                    'form' => $form->createView(),
                    'user' => $user
        ]);
    }

    /**
     * Check confirm email by token
     *
     * @param string $email
     * @param string $token
     *
     * @return RedirectResponse
     */
    public function confirmEmailAction($email, $token) {
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserByConfirmationToken($token);

        if (!$user) {
            $msg = sprintf('The user with "confirmation token" does not exist for value "%s"', $token);
            throw new NotFoundHttpException($msg);
        }

        $emailConstraint = new EmailConstraint();

        /** @var $errors \Symfony\Component\Validator\ConstraintViolationListInterface */
        $errors = $this->get('validator')->validateValue(
                $email, $emailConstraint
        );

        if ($errors->count()) {
            throw new NotFoundHttpException($errors);
        }

        $user->setEmail($email);
        $user->setConfirmationToken(null);

        $this->em->persist($user);
        $this->em->flush();

        return $this->redirectToRoute('erp_user_details');
    }

    /**
     * Delete user photo
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function deletePhotoAction(Request $request) {
        if ($request->getMethod() === 'DELETE') {
            $user = $this->getUser();
            $this->em->persist($user->setImage(null));
            $this->em->flush();

            return $this->redirect($request->headers->get('referer'));
        }
        $askMsg = 'Are you sure you want to delete photo?';

        return $this->render('ErpCoreBundle:crossBlocks:delete-confirmation-popup.html.twig', [
                    'askMsg' => $askMsg,
                    'actionUrl' => $this->generateUrl('erp_user_details_delete_photo')
        ]);
    }

    /**
     * Delete user photo
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function deleteLogoAction(Request $request) {
        if ($request->getMethod() === 'DELETE') {
            $user = $this->getUser();
            $this->em->persist($user->setLogo(null));
            $this->em->flush();

            return $this->redirect($request->headers->get('referer'));
        }
        $askMsg = 'Are you sure you want to delete company\'s logo?';

        return $this->render('ErpCoreBundle:crossBlocks:delete-confirmation-popup.html.twig', [
                    'askMsg' => $askMsg,
                    'actionUrl' => $this->generateUrl('erp_user_details_delete_logo')
        ]);
    }

    /**
     * Ask Pro widget
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function askProAction(Request $request) {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->getUser();
        if (!$user || !$user->hasRole(User::ROLE_MANAGER)) {
            throw $this->createNotFoundException();
        }

        $form = $this->createFormAskPro($user);
        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid() && !$user->isReadOnlyUser()) {
                $proRequest = $form->getData();
                $this->em->persist($proRequest);
                $this->em->flush();
                $this->get('session')->getFlashBag()->add('alert_ok', 'Your request has been successfully sent');
            } else {
                $templ = 'Ask a pro for Tip: ';
                $subjectErr = $form->get('subject')->getErrors();

                if (isset($subjectErr[0])) {
                    $this->get('session')->getFlashBag()->add(
                            'alert_error', $templ . $subjectErr[0]->getMessageTemplate()
                    );
                }

                $msgErr = $form->get('message')->getErrors();
                if (isset($msgErr[0])) {
                    $this->get('session')->getFlashBag()->add('alert_error', $templ . $msgErr[0]->getMessageTemplate());
                }
            }

            return $this->redirectToRoute('fos_user_profile_show');
        }

        return $this->render(
                        'ErpUserBundle:Profile/Widgets:ask-pro.html.twig', ['user' => $user, 'form' => $form->createView()]
        );
    }

    /**
     * Return hint
     *
     * @param $hintCode
     *
     * @return Response
     */
    public function hintContentAction($hintCode) {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->getUser();
        if (!$user) {
            throw $this->createNotFoundException();
        }

        $hintMsg = $this->get('erp.users.user.service')->getHintContent($user, $hintCode);

        return $this->render('ErpCoreBundle:crossBlocks:general-confirmation-popup.html.twig', [
                    'askMsg' => $hintMsg,
                    'hideActionBtn' => true,
                    'cancelBtn' => 'OK'
        ]);
    }

    /**
     * Display info message in the cases if manager's account Read Only
     *
     * @return Response
     */
    public function readOnlyPopupAction() {
        $msg = 'Please enter Bank or Cards information to your profile and wait for account activation by Admin.';

        return $this->render('ErpCoreBundle:crossBlocks:general-confirmation-popup.html.twig', [
                    'askMsg' => $msg,
                    'hideActionBtn' => true,
                    'cancelBtn' => 'OK'
        ]);
    }

    /**
     * Send email to confirm email
     *
     * @param User   $user
     * @param string $email
     *
     * @return bool
     */
    protected function sendEmailToConfirmEmail(User $user, $email) {
        $emailParams = [
            'sendTo' => $user->getEmail(),
            'url' => $this->generateUrl('erp_user_confirm_email', [
                'email' => $email,
                'token' => $user->getConfirmationToken()
                    ], UrlGeneratorInterface::ABSOLUTE_URL
            ),
        ];

        $emailType = EmailNotificationFactory::TYPE_USER_CHANGE_EMAIL;
        $sentStatus = $this->container->get('erp.core.email_notification.service')->sendEmail($emailType, $emailParams);

        return $sentStatus;
    }

    /**
     * Create message form
     *
     * @param User $user
     *
     * @return \Symfony\Component\Form\Form
     */
    private function createFormMessageForm(User $user) {
        /* form message */
        $actionFormMessage = $this->generateUrl('erp_user_messages', ['toUserId' => $user->getId()]);
        $formMessageOptions = ['action' => $actionFormMessage, 'method' => 'POST'];
        $formMessage = $this->createForm(new MessagesFormType(), null, $formMessageOptions);

        return $formMessage;
    }

    /**
     * Create Service Request form
     *
     * @return \Symfony\Component\Form\Form
     */
    private function createFormServiceRequest() {
        $toUser = $this->getUser()->getTenantProperty()->getUser();
        $actionFormServiceRequest = $this->generateUrl('erp_user_service_request', ['toUserId' => $toUser->getId()]);
        $formServiceRequestOptions = ['action' => $actionFormServiceRequest, 'method' => 'POST'];

        $serviceRequestTypes = $this->get('erp.users.user.service')->getServiceRequestTypes();
        $formServiceRequest = $this->createForm(
                new DashboardServiceRequestFormType($serviceRequestTypes), null, $formServiceRequestOptions
        );

        return $formServiceRequest;
    }

    /**
     * Create Ask a pro for Tip form
     *
     * @param User $user
     *
     * @return \Symfony\Component\Form\Form
     */
    private function createFormAskPro(User $user) {
        $fees = $this->get('erp.core.fee.service')->getFees();
        $servicingFee = $fees ? $fees->getAskProCheck() : 0;

        $proRequest = new ProRequest();
        $proRequest->setServicingFee($servicingFee)
                ->setUser($user)
                ->setStatus(ProRequest::STATUS_IN_PROCESS);

        $formOptions = ['action' => $this->generateUrl('erp_user_profile_ask_pro'), 'method' => 'POST'];
        $form = $this->createForm(new AskProFormType(), $proRequest, $formOptions);

        return $form;
    }

}
