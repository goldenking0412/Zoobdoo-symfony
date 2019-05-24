<?php

namespace Erp\PropertyBundle\Controller;

use Erp\CoreBundle\Controller\BaseController;
use Erp\CoreBundle\EmailNotification\EmailNotificationFactory;
use Erp\CoreBundle\Exception\PropertyNotFoundException;
use Erp\PropertyBundle\Entity\AppointmentRequest;
use Erp\PropertyBundle\Entity\Property;
use Erp\PropertyBundle\Entity\PropertySettings;
use Erp\PropertyBundle\Entity\PropertyRepostRequest;
use Erp\UserBundle\Entity\InvitedUser;
use Erp\UserBundle\Entity\User;
use Erp\PropertyBundle\Form\Type\AppointmentRequestFormType;
use Erp\PropertyBundle\Form\Type\InviteTenantFormType;
use Erp\PropertyBundle\Form\Type\PropertySettingsType;
use Erp\PropertyBundle\Model\PropertyFilter;
use Erp\PropertyBundle\Form\Type\PropertyFilterFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Erp\PropertyBundle\Entity\PropertySecurityDeposit;
use Erp\PropertyBundle\Entity\LeaseOutHistory;
use Erp\PropertyBundle\Form\Type\PropertyRefundDepositType;
use Erp\StripeBundle\Helper\ApiHelper;
use Erp\PaymentBundle\Entity\StripeCustomer;
use Erp\PaymentBundle\Entity\StripeAccount;
use Erp\PropertyBundle\Entity\ScheduledRentPayment;

/**
 * Class PropertyController
 *
 * @package Erp\PropertyBundle\Controller
 */
class PropertyController extends BaseController {

    /**
     * Available properties page
     *
     * @param Request $request
     *
     * @return Response
     */
    public function availableAction(Request $request) {
        
        $this->processPropertyFilters($request);

        $form = $this->createPropertyFiltersForm(null, PropertyFilter::FORM_AVAILABLE_TYPE);
        $form->handleRequest($request);

        $properties = $this->em->getRepository('ErpPropertyBundle:Property')->findAvailable($this->getUser());

        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addItem('Home', $this->get('router')->generate('erp_site_homepage'));
        $breadcrumbs->addItem('Available properties');

        return $this->render(
                        'ErpPropertyBundle:Available:index.html.twig', ['properties' => $properties, 'form' => $form->createView()]
        );
    }

    /**
     * Property search page
     *
     * @param Request $request
     *
     * @return Response
     */
    public function searchAction(Request $request) {
        $this->processPropertyFilters($request);

        $form = $this->createPropertyFiltersForm();
        $form->handleRequest($request);
        $propertyFilter = $form->getData();

        if ($propertyFilter->getCityId()) {
            $city = $this->em->getRepository('ErpCoreBundle:City')->find($propertyFilter->getCityId());
            $propertyFilter->setCity($city);
        }

        $properties = $this->em->getRepository('ErpPropertyBundle:Property')->getBySearchParams($propertyFilter);
        $pageUrl = $request->getRequestUri();

        $withParams = $pageUrl !== $request->getBaseUrl() . $request->getPathInfo();

        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addItem('Home', $this->get('router')->generate('erp_site_homepage'));
        $breadcrumbs->addItem('Available Properties', $this->get('router')->generate('erp_property_available'));
        $breadcrumbs->addItem('Search');

        return $this->render(
                        'ErpPropertyBundle:Search:index.html.twig', [
                    'form' => $form->createView(),
                    'properties' => $properties,
                    'propertyFilter' => $propertyFilter,
                    'pageUrl' => $pageUrl,
                    'isPageWithParams' => $withParams
                        ]
        );
    }

    /**
     * Property details page
     *
     * @param string $stateCode
     * @param string $cityName
     * @param string $name
     *
     * @return Response
     * @throws PropertyNotFoundException
     */
    public function detailsAction($stateCode, $cityName, $name) {
        /** @var User $user */
        $user = $this->getUser();
        $city = $this->em->getRepository('ErpCoreBundle:City')
                ->findOneBy(['stateCode' => $stateCode, 'name' => $cityName]);

        $filter = [
            'city' => $city,
            'name' => $name,
            'status' => [Property::STATUS_AVAILABLE, Property::STATUS_DRAFT],
        ];

        if ($user && ($user->hasRole('ROLE_SUPER_ADMIN') || $user->hasRole('ROLE_ADMIN'))) {
            unset($filter['status']);
        }

        /** @var $property \Erp\PropertyBundle\Entity\Property */
        $property = $this->em->getRepository('ErpPropertyBundle:Property')
                ->findOneBy($filter);

        if (!$property) {
            throw new NotFoundHttpException('Property not found');
        }

        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addItem('Home', $this->get('router')->generate('erp_site_homepage'));
        $breadcrumbs->addItem('Available Properties', $this->get('router')->generate('erp_property_available'));
        $breadcrumbs->addItem($property->getName());

        return $this->render(
                        'ErpPropertyBundle:Property:details.html.twig', ['property' => $property, 'currentUser' => $user]
        );
    }

    /**
     * Get property description
     *
     * @param int $propertyId
     *
     * @return Response
     * @throws PropertyNotFoundException
     */
    public function descriptionAction($propertyId) {
        /** @var $property \Erp\PropertyBundle\Entity\Property */
        $property = $this->em->getRepository('ErpPropertyBundle:Property')->find($propertyId);
        if (!$property) {
            throw new PropertyNotFoundException;
        }

        return $this->render(
                        'ErpPropertyBundle:Property:description.html.twig', ['modalTitle' => 'Amenities', 'property' => $property]
        );
    }

    /**
     * Property - Request an Appointment
     *
     * @param int     $propertyId
     * @param Request $request
     *
     * @return Response
     * @throws PropertyNotFoundException
     */
    public function appointmentRequestAction(Request $request, $propertyId) {
        /** @var $property \Erp\PropertyBundle\Entity\Property */
        $property = $this->em->getRepository('ErpPropertyBundle:Property')->find($propertyId);
        if (!$property) {
            throw new PropertyNotFoundException;
        }

        $appointment = new AppointmentRequest();
        $form = $this->createAppointmentRequestForm($appointment->setProperty($property));

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $appointment = $form->getData();
                $manager = $property->getUser();
                $isSent = $this->sendAppointmentRequestEmail($appointment, $manager->getEmail());

                if ($isSent) {
                    $this->em->persist($appointment);
                    $this->em->flush();
                    $this->get('session')->getFlashBag()->add('alert_ok', 'Request an Appointment has been sent!');
                } else {
                    $this->get('session')->getFlashBag()->add(
                            'alert_error', 'An error has occured while send email. Please, try later'
                    );
                }

                return $this->redirectToRoute(
                                'erp_property_page', [
                            'stateCode' => $property->getCity()->getStateCode(),
                            'cityName' => $property->getCity()->getName(),
                            'name' => $property->getName()
                                ]
                );
            }
        }

        $propertyUrl = $this->generateUrl(
                'erp_property_page', [
            'stateCode' => $property->getStateCode(),
            'cityName' => $property->getCity()->getName(),
            'name' => $property->getName()
                ]
        );
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addItem('Home', $this->generateUrl('erp_site_homepage'))
                ->addItem('Available Properties', $this->generateUrl('erp_property_available'))
                ->addItem($property->getName(), $propertyUrl)
                ->addItem('Request an Appointment');

        return $this->render(
                        'ErpPropertyBundle:Property:appointment-request.html.twig', ['form' => $form->createView(), 'property' => $property]
        );
    }

    /**
     * Property - invite tenant
     *
     * @param Request $request
     * @param int     $propertyId
     *
     * @return Response
     * @throws PropertyNotFoundException
     */
    public function inviteTenantAction(Request $request, $propertyId) {
        /** @var $user User */
        $user = $this->getUser();
        if (!$user || !$user->hasRole(User::ROLE_MANAGER)) {
            return $this->redirectToRoute('fos_user_security_login');
        }
        /** @var $property \Erp\PropertyBundle\Entity\Property */
        $property = $this->em->getRepository('ErpPropertyBundle:Property')->find($propertyId);
        if (!$property) {
            throw $this->createNotFoundException();
        }

        $invitedUser = new InvitedUser();
        $form = $this->createInviteTenantForm($invitedUser->setProperty($property));
        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->email = $form->get('invitedEmail')->getData();

                $existUser = $this->em->getRepository('ErpUserBundle:User')
                        ->findOneBy(['email' => $this->email]);
                $invitedUsers = $this->em->getRepository('ErpUserBundle:InvitedUser')
                        ->findOneBy(['invitedEmail' => $this->email]);
                
                if (($existUser instanceof User && $existUser->isEnabled()) || $invitedUsers) {
                    $this->get('session')->getFlashBag()
                            ->add('alert_error', 'Tenant you are trying to add was already linked to other property');
                } else {
                    if ($existUser instanceof User && !$existUser->isEnabled() && $existUser->hasRole(User::ROLE_TENANT)) {
                        $userService = $this->get('erp.users.user.service');
                        $userService->activateUser($existUser);
                        $property->setTenantUser($existUser);
                        $this->em->persist($property->setStatus(Property::STATUS_RENTED));

                        $this->sendAssignTenantEmail($existUser);
                    } elseif ($existUser instanceof User && !$existUser->isEnabled() && $existUser->hasRole(User::ROLE_MANAGER)) {
                        $this->get('session')->getFlashBag()
                                ->add('alert_error', 'Email is disabled. Contact Administrator.');
                    } else {
                        $invitedUser = $form->getData();
                        $invitedUser->setProperty($property)
                                ->setInviteCode($this->get('fos_user.util.token_generator')->generateToken())
                                ->setIsUse(false);
                        
                        $this->em->persist($invitedUser);
                        $this->em->persist($property->setStatus(Property::STATUS_RENTED));

                        $this->sendInviteTenantEmail($invitedUser);
                    }

                    $this->em->flush();
                }
            } else {
                $this->emailErr = $form->get('invitedEmail')->getErrors();
                if ($this->emailErr) {
                    $this->get('session')->getFlashBag()->add('alert_error', $this->emailErr[0]->getMessageTemplate());
                }
            }

            return $this->redirect($request->headers->get('referer'));
        }

        return $this->render('ErpPropertyBundle:Property:invite-tenant.html.twig', array(
                    'form' => $form->createView(),
                    'user' => $user
        ));
    }

    /**
     * Remove tenant with status pending
     *
     * @param Request $request
     * @param int     $propertyId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function closeOutAction(Request $request, $propertyId) {
        /** @var User $user */
        $user = $this->getUser();

        $askMsg = 'Are you sure you want to Close Out the Lease?';

        /** @var Property $property */
        $property = $this->em->getRepository('ErpPropertyBundle:Property')
                ->findOneBy(['id' => $propertyId, 'user' => $user]);

        if (!$property) {
            throw new NotFoundHttpException('No permissions');
        }

        if ($request->getMethod() === 'POST') {

            $leaseOutHistory = new LeaseOutHistory();

            $data = array(
                'data' => array(
                    'property' => $property,
                    'tenants' => $property->getTenantUser(),
                    'depositAccount' => $property->getDepositAccount(),
                    'securityDeposit' => $property->getSecurityDeposit(),
                    'settings' => $property->getSettings(),
                    'invitedUser' => $property->getInvitedUsers(),
                    'history' => $property->getHistory()
                )
            );

            $leaseOutHistory->setPropertyData(serialize($data));
            $this->em->persist($leaseOutHistory);
            $this->em->flush();

            // remove tenants
            // remove banking
            // remove security deposit
            // remove payment settings
            // remove deposit account
            $property->setTenantUser(null);
            $property->setDepositAccount(null);
            $property->setSecurityDeposit(null);
            $property->setSettings(null);

            $appointmentRequests = $property->getAppointmentRequests();
            if (count($appointmentRequests) > 0) {
                foreach ($appointmentRequests as $el) {
                    $property->getAppointmentRequests()->removeElement($el);
                    $this->em->remove($el);
                    $this->em->persist($el);
                }
                $this->em->flush();
            }

            $invitedUsers = $property->getInvitedUsers();
            if (count($invitedUsers) > 0) {
                foreach ($invitedUsers as $el) {
                    $property->getInvitedUsers()->removeElement($el);
                    $this->em->remove($el);
                    $this->em->persist($el);
                }
                $this->em->flush();
            }

            $propertyRepostRequests = $property->getPropertyRepostRequests();
            if (count($propertyRepostRequests) > 0) {
                foreach ($propertyRepostRequests as $el) {
                    $property->getPropertyRepostRequests()->removeElement($el);
                    $this->em->remove($el);
                    $this->em->persist($el);
                }
                $this->em->flush();
            }

            $history = $property->getHistory();
            if (count($history) > 0) {
                foreach ($history as $el) {
                    $property->getPropertyRepostRequests()->removeElement($el);
                    $this->em->remove($el);
                    $this->em->persist($el);
                }
                $this->em->flush();
            }
            $property->setStatus(Property::STATUS_AVAILABLE);
            $this->em->persist($property);
            $this->em->flush();
            return $this->redirect($request->headers->get('referer'));
        }

        return $this->render('ErpCoreBundle:crossBlocks:general-confirmation-popup.html.twig', [
                    'askMsg' => $askMsg,
                    'actionUrl' =>
                    $this->generateUrl('erp_property_close_out', ['propertyId' => $propertyId])
                ])
        ;
    }

    /**
     * Refund Deposit Property page
     *
     * @param Request $request
     * @param int|null $propertyId
     *
     * @return RedirectResponse|Response|NotFoundHttpException
     */
    public function refundDepositAction(Request $request, $propertyId) {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->getUser();

        $property = $this->em->getRepository('ErpPropertyBundle:Property')->getPropertyByUser($user, $propertyId);
        if (!$property) {
            throw $this->createNotFoundException();
        }

        $pageNumber = $request->get('page', 1);
        $securityDeposit = $property->getSecurityDeposit();
        if (!$securityDeposit) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(new PropertyRefundDepositType(), ['startPaymentAt' => new \Datetime(), 'amount' => $securityDeposit->getAmount()]);
        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $refund = $form->getData();
                $amount = $refund['amount'];

                if ($refund['refund'] == 1) {
                    if ($amount < $securityDeposit->getAmount()) {
                        $status = PropertySecurityDeposit::STATUS_DEPOSIT_REFUNDED_PARTIAL;
                    } else {
                        $status = PropertySecurityDeposit::STATUS_DEPOSIT_REFUNDED_TOTAL;
                    }
                } else {
                    $status = PropertySecurityDeposit::STATUS_DEPOSIT_REFUNDED_NO;
                    $securityDeposit->setRefundedAmount($amount);
                    $securityDeposit->setStatus($status);
                    $this->em->persist($securityDeposit);
                    $this->em->flush();
                }
                if ($refund['refund'] == 1) {
                    if ($property->getTenantUser() && $property->getTenantUser()->hasStripeCustomers()) {
                        $tenantStripeCustomer = $property->getTenantUser()->getStripeCustomer();
                    } else {
                        $this->addFlash(
                                'alert_error', 'No Tenant Refund Account defined'
                        );
                        return $this->redirectToRoute('erp_property_refund_deposit', ['propertyId' => $propertyId]);
                    }

                    if ($property->getUser() && $property->getUser()->hasStripeCustomers()) {
                        $managerStripeCustomer = $property->getUser()->getStripeCustomer();
                        $managerStripeAccount = $property->getUser()->getStripeAccount();
                    } else {
                        $this->addFlash(
                                'alert_error', 'No Manager Refund Account defined'
                        );
                        return $this->redirectToRoute('erp_property_refund_deposit', ['propertyId' => $propertyId]);
                    }
                    
                    $response = $this->get('erp.payment.service')->makeSinglePayment($managerStripeCustomer, $amount, array(), $managerStripeAccount, $tenantStripeCustomer);

                    if (!$response->isSuccess()) {
                        $status = $securityDeposit->getStatus(); // ERROR STATUS LEFT UNTOUCH
                        $logger = $this->get('logger');
                        $logger->critical(json_encode($response->getErrorMessage()));
                        $this->addFlash('alert_error', 'Error');
                        return $this->redirectToRoute('erp_property_refund_deposit', ['propertyId' => $propertyId]);
                    } else {
                        $this->addFlash('alert_ok', 'Success');
                    }

                    $securityDeposit->setRefundedAmount($amount);
                    $securityDeposit->setStatus($status);
                    $this->em->persist($securityDeposit);
                    $this->em->flush();
                }
                return $this->redirectToRoute('erp_property_listings_all');
            }

            $this->addFlash(
                    'alert_error', $form->getErrors(true)[0]->getMessage()
            );
            return $this->redirectToRoute('erp_property_refund_deposit', ['propertyId' => $propertyId]);
        }

        return $this->render('ErpPropertyBundle:Property:refund-deposit.html.twig', array(
                    'user' => $user,
                    'form' => $form->createView(),
                    'property' => $property,
                    'page' => $pageNumber,
                    'securityDeposit' => $securityDeposit,
        ));
    }

    /**
     * Process filters fotm
     *
     * @param Request $request
     *
     * @return $this
     */
    protected function processPropertyFilters(Request $request) {
        $state = $this->getStateFromRequest($request);
        !$state ?: $this->get('erp.core.location')->setCities($state);

        return $this;
    }

    /**
     * Get state name from request
     *
     * @param Request $request
     *
     * @return null
     */
    protected function getStateFromRequest(Request $request) {
        $filterParams = $request->query->get('properties', []);
        $state = isset($filterParams['state']) ? $filterParams['state'] : null;

        return $state;
    }

    /**
     * Get filter param from request
     *
     * @param string $paramName
     *
     * @return null
     */
    protected function getFilterParamFromRequest($paramName) {
        $filterParams = $this->getRequest()->query->get('properties', []);
        $param = isset($filterParams[$paramName]) ? $filterParams[$paramName] : null;

        return $param;
    }

    /**
     * Create Property filters form
     *
     * @param null|string $action
     * @param string      $type
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function createPropertyFiltersForm($action = null, $type = PropertyFilter::FORM_SEARCH_TYPE) {
        $action = $action ? $action : $this->generateUrl('erp_property_search');
        $formOptions = ['action' => $action, 'method' => 'GET'];

        $form = $this->createForm(
                new PropertyFilterFormType($this->container, $type), new PropertyFilter(), $formOptions
        );

        return $form;
    }

    /**
     * Create Appointment Request form
     *
     * @param AppointmentRequest $appointmentRequest
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function createAppointmentRequestForm(AppointmentRequest $appointmentRequest) {
        $formOptions = [
            'action' => $this->generateUrl(
                    'erp_property_appointment_request', ['propertyId' => $appointmentRequest->getProperty()->getId()]
            ),
            'method' => 'POST'
        ];
        $form = $this->createForm(new AppointmentRequestFormType(), $appointmentRequest, $formOptions);

        return $form;
    }

    /**
     * Create Invite Tenant form
     *
     * @param InvitedUser $invitedUser
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function createInviteTenantForm(InvitedUser $invitedUser) {
        $action = $this->generateUrl(
                'erp_property_invite_tenant', ['propertyId' => $invitedUser->getProperty()->getId()]
        );
        $formOptions = ['action' => $action, 'method' => 'POST'];
        $form = $this->createForm(new InviteTenantFormType(), $invitedUser, $formOptions);

        return $form;
    }

    /**
     * Send appointment request email
     *
     * @param AppointmentRequest $appointmentRequest
     * @param string             $toEmail
     *
     * @return mixed
     */
    protected function sendAppointmentRequestEmail(AppointmentRequest $appointmentRequest, $toEmail) {
        $url = $this->generateUrl(
                'erp_property_page', [
            'stateCode' => $appointmentRequest->getProperty()->getCity()->getStateCode(),
            'cityName' => $appointmentRequest->getProperty()->getCity()->getName(),
            'name' => $appointmentRequest->getProperty()->getName(),
                ], UrlGeneratorInterface::ABSOLUTE_URL
        );

        $this->emailParams = [
            'sendTo' => $toEmail,
            'appointmentRequest' => $appointmentRequest,
            'url' => $url,
        ];

        $this->emailType = EmailNotificationFactory::TYPE_APPOINTMENT_REQUEST;
        $sentStatus = $this->get('erp.core.email_notification.service')->sendEmail($this->emailType, $this->emailParams);

        return $sentStatus;
    }

    /**
     * Send invite tenant email
     *
     * @param InvitedUser $invitedUser
     *
     * @return bool
     */
    protected function sendInviteTenantEmail(InvitedUser $invitedUser) {
        $url = $this->generateUrl(
                'erp_user_tenant_registration', ['token' => $invitedUser->getInviteCode()], UrlGeneratorInterface::ABSOLUTE_URL
        );

        $user = $this->getUser();
        $this->emailParams = [
            'sendTo' => $invitedUser->getInvitedEmail(),
            'mailFromTitle' => $user->getFromForEmail(),
            'preSubject' => $user->getSubjectForEmail(),
            'url' => $url,
            'invitedUser' => $invitedUser
        ];

        $sentStatus = $this->get('erp.core.email_notification.service')
                ->sendEmail(EmailNotificationFactory::TYPE_INVITE_TENANT_USER, $this->emailParams);

        return $sentStatus;
    }

    /**
     * Send assign tenant email
     *
     * @param object $user
     *
     * @return bool
     */
    protected function sendAssignTenantEmail($user) {
        $url = $this->generateUrl(
                'fos_user_security_login', [], UrlGeneratorInterface::ABSOLUTE_URL
        );

        $currentUser = $this->getUser();
        $this->emailParams = [
            'sendTo' => $user->getEmail(),
            'url' => $url,
            'mailFromTitle' => $currentUser->getFromForEmail(),
            'preSubject' => $currentUser->getSubjectForEmail(),
        ];

        $sentStatus = $this->get('erp.core.email_notification.service')
                ->sendEmail(EmailNotificationFactory::TYPE_ASSIGN_TENANT_USER, $this->emailParams);

        return $sentStatus;
    }

}
