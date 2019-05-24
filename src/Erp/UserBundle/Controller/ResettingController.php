<?php

namespace Erp\UserBundle\Controller;

use Erp\CoreBundle\EmailNotification\EmailNotificationFactory;
use Erp\UserBundle\Entity\User;
use Erp\UserBundle\Form\Type\ResettingFormType;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Controller\ResettingController as BaseController;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ResettingController
 *
 * @package Erp\UserBundle\Controller
 */
class ResettingController extends BaseController {

    /**
     * Request reset user password: submit form and send email
     *
     * @param Request $request
     *
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function sendEmailAction(Request $request) {
        $username = $request->request->get('username');
        /** @var $user UserInterface */
        $user = $this->get('fos_user.user_manager')->findUserByUsernameOrEmail($username);

        $returnParams = [];

        if (!$user) {
            $returnParams = ['invalid_username' => $username];
        } elseif ($user->getStatus() == User::STATUS_DISABLED || !$user->isEnabled() || $user->getStatus() == User::STATUS_REJECTED
        ) {
            $returnParams = ['user_disabled' => true];
        }

        if ($returnParams) {
            return $this->render('FOSUserBundle:Resetting:request.html.twig', $returnParams);
        }

        if ($user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) {
            return $this->render('FOSUserBundle:Resetting:passwordAlreadyRequested.html.twig');
        }

        if (null === $user->getConfirmationToken()) {
            $user->setConfirmationToken($this->get('fos_user.util.token_generator')->generateToken());
        }

        $this->sendResettingEmailMessage($user);
        $user->setPasswordRequestedAt(new \DateTime());
        $this->get('fos_user.user_manager')->updateUser($user);

        return $this->redirectToRoute('fos_user_resetting_check_email', array('email' => $this->getObfuscatedEmail($user)));
    }

    /**
     * Reset user password
     *
     * @param Request                                   $request
     * @param                                           $token
     *
     * @return RedirectResponse|Response
     * @throws NotFoundHttpException
     */
    public function resetAction(Request $request, $token) {
        $formFactory = $this->get('fos_user.resetting.form.factory');
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user = $userManager->findUserByConfirmationToken($token);
        if (!$user) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }

        if ($user->getStatus() == User::STATUS_DISABLED or $user->getStatus() == User::STATUS_REJECTED) {
            throw new NotFoundHttpException('Account is disabled. Please contact site Administrator.');
        }

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_INITIALIZE, $event);
        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm();
        $form->setData($user);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_SUCCESS, $event);
            $userManager->updateUser($user);

            $response = $event->getResponse();

            if (!$response) {
                $response = new RedirectResponse($this->getUserUrlToRedirect($user));
            }

            $dispatcher->dispatch(
                    FOSUserEvents::RESETTING_RESET_COMPLETED, new FilterUserResponseEvent($user, $request, $response)
            );
        } else {
            $response = $this->render('FOSUserBundle:Resetting:reset.html.twig', array(
                'token' => $token,
                'form' => $form->createView(),
            ));
        }

        return $response;
    }

    /**
     * Send resetting password mail
     *
     * @param \Erp\UserBundle\Entity\User $user
     *
     * @return mixed
     */
    protected function sendResettingEmailMessage(User $user) {
        $emailParams = [
            'sendTo' => $user->getEmail(),
            'url' => $this->generateUrl('fos_user_resetting_reset', ['token' => $user->getConfirmationToken()], true)
        ];
        $emailType = EmailNotificationFactory::TYPE_USER_PASSWORD_RESETTING;
        $isSend = $this->get('erp.core.email_notification.service')->sendEmail($emailType, $emailParams);

        return $isSend;
    }

    /**
     * Generate url to redirect
     *
     * @param User $user
     *
     * @return string
     */
    private function getUserUrlToRedirect(User $user) {
        $url = ($user->hasRole(User::ROLE_ADMIN) || $user->hasRole(User::ROLE_SUPER_ADMIN)) ? 'sonata_admin_dashboard' : 'erp_user_profile_dashboard';

        return $this->generateUrl($url);
    }

}
