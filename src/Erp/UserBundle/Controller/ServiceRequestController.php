<?php

namespace Erp\UserBundle\Controller;

use Erp\CoreBundle\Controller\BaseController;
use Erp\CoreBundle\Entity\EmailNotification;
use Erp\CoreBundle\Event\EmailNotificationEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Erp\UserBundle\Entity\User;
use Erp\UserBundle\Entity\ServiceRequest;
use Erp\UserBundle\Form\Type\DashboardServiceRequestFormType;
use Erp\UserBundle\Entity\ServiceRequestDocument;
use Twilio\Rest\Client;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class ServiceRequestController
 *
 * @package Erp\UserBundle\Controller
 */
class ServiceRequestController extends BaseController {

    /**
     * Page message
     *
     * @param int $toUserId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function indexAction(Request $request, $toUserId = null) {
        /** @var User $user */
        $user = $this->getUser();
        $companions = $this->getCompanions($user);

        if (count($companions) == 0) {
            return $this->render('ErpUserBundle:ServiceRequests:service-requests.html.twig');
        }


        if ($toUserId === null && count($companions)) {
            return $this->redirectToRoute('erp_user_service_request', ['toUserId' => $companions[0]->getId()]);
        }
        /** @var User $toUser */
        $toUser = ($toUserId) ? $this->em->getRepository('ErpUserBundle:User')->findOneBy(['id' => $toUserId]) : null;

        if (!$toUser || ($user->hasRole(User::ROLE_MANAGER) && !$user->isTenant($toUser)) || ($user->hasRole(User::ROLE_TENANT) && $user->getTenantProperty()->getUser() != $toUser)) {
            throw $this->createNotFoundException();
        } else {
            $messages = $this->getServiceRequests($user, $toUser);
            $message = new ServiceRequest();
            $message->setSendSms(false);
            $subject = '';
            if (count($messages) > 0) {
                $message->setSubject($messages[0]->getSubject());
                $message->setSendSms($messages[0]->getSendSms());
            }

            $serviceRequest = new ServiceRequest();
            $serviceRequestTypes = $this->get('erp.users.user.service')->getServiceRequestTypes();
            $showSendSms = $user->hasRole(User::ROLE_MANAGER);
            $action = $this->generateUrl('erp_user_service_request', ['toUserId' => $toUserId]);
            $formOptions = ['action' => $action, 'method' => 'POST', 'showSendSms' => $showSendSms];
            $form = $this->createForm(new DashboardServiceRequestFormType($serviceRequestTypes), $serviceRequest, $formOptions);

            if ($request->getMethod() === 'POST') {
                $this->submitServiceRequest($request, $user, $toUser, $form);
                return $this->redirectToRoute('erp_user_service_request', ['toUserId' => $toUserId]);
            }

            foreach ($companions as $key => $companion) {
                $companions[$key] = [
                    $companion,
                    'totalMessages' => $this->getTotalMessagesByToUser($user, $companion)
                ];
            }

            $this->setUnreadMessages($user, $messages);
            $groups = [];
            foreach ($messages as $v) {
                $groups[$v->getSubject()][] = $v;
            }

            $renderParams = [
                'form' => $form->createView(),
                'companions' => $companions,
                'currentCompanion' => $toUser,
                'messages' => $messages,
                'showSendSms' => $showSendSms,
                'groups' => $groups,
                'requestTypes' => $serviceRequestTypes
            ];
        }

        return $this->render('ErpUserBundle:ServiceRequests:service-requests.html.twig', $renderParams);
    }

    /**
     * Submit data from tenant to manager
     *
     * @param Request $request
     * @param User $user Could be a tenant or a manager
     * @param User $toUser
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function submitServiceRequest(Request $request, User $sender, User $toUser, \Symfony\Component\Form\Form $form) {
        $form->handleRequest($request);

        // $serviceRequest->setFromUser($sender);
        // $serviceRequest->setToUser($toUser);

        $teml = 'Service Request: ';

        if ($form->isValid()) {
            $serviceRequest = $form->getData();

            $currentDate = new \DateTime();
            $selectedDateString = $form->get('date')->getData();
            $selectedDate = new \DateTime($selectedDateString);
            $selectedDate->add(new \DateInterval('PT23H59M59S'));

            $files = $form['attachments']->getData();
            foreach ($files as $k => $file) {
                if ($file) {
                    // Generate a unique name for the file before saving it
                    $fileName = md5(uniqid()) . '.' . $file->guessExtension();

                    // Move the file to the directory where brochures are stored
                    $r = $file->move(
                            'uploads/service_request/', $fileName
                    );

                    // Update the 'brochure' property to store the PDF file name
                    // instead of its contents
                    $serviceRequestDocument = new ServiceRequestDocument();
                    $serviceRequestDocument->setName($fileName);
                    $serviceRequestDocument->setOriginalName($file->getClientOriginalName());
                    $serviceRequestDocument->setServiceRequest($serviceRequest);
                    $this->em->persist($serviceRequestDocument);
                    $serviceRequest->addDocument($serviceRequestDocument);
                } /*  else {
                  // do nothing
                  } */
            }

            if ($selectedDate >= $currentDate) {
                $serviceRequest->setDate($selectedDate);
                $serviceRequest->setFromUser($sender)->setToUser($toUser);
                $toNumber = '+1' . $toUser->getPhoneDigitsOnly();
                $fromNumber = $this->getParameter('twilio_number');
                $serviceRequest->setToNumber($toNumber);
                $serviceRequest->setFromNumber($fromNumber);
                $this->em->persist($serviceRequest);
                $this->em->flush();

                $event = new EmailNotificationEvent($toUser, EmailNotification::SETTING_SERVICE_REQUESTS, [
                    '#url#' => $this->generateUrl('erp_user_service_request', ['toUserId' => $sender->getId()], true)
                ]);

                /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
                $dispatcher = $this->get('event_dispatcher');
                $dispatcher->dispatch(EmailNotification::EVENT_SEND_EMAIL_NOTIFICATION, $event);

                $this->sendTwilioMessage($serviceRequest);

                $this->get('session')->getFlashBag()->add(
                        'alert_ok', $teml . 'has been successfully sent'
                );
            } else {
                $this->get('session')->getFlashBag()->add(
                        'alert_error', $teml . 'incorrect date'
                );
            }
        } else {
            $errors = array();

            foreach ($form->all() as $child) {
                foreach ($child->getErrors() as $error) {
                    $errors[] = $error->getMessageTemplate();
                }
            }

            foreach ($errors as $error) {
                $this->get('session')->getFlashBag()->add('alert_error', $teml . $error);
            }
        }

        return $this->redirectToRoute('erp_user_service_request');
    }

    /**
     * Return list service requests
     *
     * @param User $user
     * @param User $toUser
     *
     * @return array
     */
    protected function getServiceRequests(User $user, User $toUser) {
        $serviceRequests = $this->em->getRepository('ErpUserBundle:ServiceRequest')->getServiceRequests($user, $toUser);

        return $serviceRequests;
    }

    /**
     * Return list tenants by manager
     *
     * @param User $user
     *
     * @return array
     */
    protected function getTenantsByManager(User $user) {
        $tenants = $this->em->getRepository('ErpUserBundle:ServiceRequest')->getTenantsByManager($user);

        return $tenants;
    }

    /**
     * Return list companions
     *
     * @param User $user
     *
     * @return array
     */
    protected function getCompanions(User $user) {
        // Get companions
        if ($user->hasRole(User::ROLE_MANAGER)) {
            $companions = $user->getTenants();
        } elseif ($user->hasRole(User::ROLE_LANDLORD)) {
            // $companions = $this->em->getRepository(User::REPOSITORY)->findTenantsOfLandlord($user);
            // $companions = $this->em->getRepository(User::REPOSITORY)->findManagersAndTenantsOfLandlord($user);
            $companions = $this->em->getRepository(User::REPOSITORY)->findManagersOfLandlord($user);
        } else {
            if ($user->getTenantProperty()) {
                $companions = array($user->getTenantProperty()->getUser());
            }
        }

        return $companions;
    }

    /**
     * Return count messages for user
     *
     * @param User $fromUser
     * @param User $toUser
     *
     * @return int
     */
    protected function getTotalMessagesByToUser(User $fromUser, User $toUser) {
        $totalMessages = $this->getDoctrine()
                ->getRepository('ErpUserBundle:ServiceRequest')
                ->getTotalMessagesByToUser($fromUser, $toUser)
        ;

        return $totalMessages;
    }

    /**
     * Set unread messages
     *
     * @param $messages
     *
     * @return bool
     */
    protected function setUnreadMessages(User $user, $messages) {
        if ($messages) {
            foreach ($messages as $message) {
                if ($message->getToUser() === $user) {
                    $message->setIsRead(true);
                    $this->em->persist($message);
                    $this->em->flush();
                }
            }
        }

        return true;
    }

    /**
     * 
     * @param type $message
     */
    protected function sendTwilioMessage($message) {
        if ($message->getSendSms()) {
            $sid = $this->getParameter('twilio_sid');
            $token = $this->getParameter('twilio_auth_token');
            $twilio = new Client($sid, $token);
            if (strlen($message->getToNumber()) != 12) {
                $this->get('session')->getFlashBag()->add('alert_error', 'The destination number is incorrect');
            }
            if (strlen($message->getFromNumber()) != 12) {
                $this->get('session')->getFlashBag()->add('alert_error', 'Your phone number is incorrect');
            }
            //var_dump($message->getFromNumber(), $message->getToNumber());die();
            try {
                $m = $twilio->messages->create(
                        $message->getToNumber(), array(
                    "body" => $message->getSubject() . '-' . $message->getText(),
                    "from" => $message->getFromNumber(),
                        )
                );
            } catch (\Exception $e) {
                $this->get('session')->getFlashBag()->add('alert_error', $e->getCode() . ' - ' . $e->getMessage());
            }
        }
    }

}
