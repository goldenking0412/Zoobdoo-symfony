<?php

namespace Erp\AdminBundle\Controller;

use Erp\CoreBundle\EmailNotification\EmailNotificationFactory;
use Erp\PropertyBundle\Entity\Property;
use Erp\UserBundle\Entity\InvitedUser;
use Erp\UserBundle\Entity\User;
use Sonata\AdminBundle\Controller\CRUDController as BaseController;
use Sonata\AdminBundle\Exception\ModelManagerException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class InviteTenantController extends BaseController
{
    /**
     * Create action.
     *
     * @return Response
     *
     * @throws AccessDeniedException If access is not granted
     */
    public function createAction()
    {
        $templateKey = 'edit';
        $error = [];

        if (false === $this->admin->isGranted('CREATE')) {
            throw new AccessDeniedException();
        }

        $object = $this->admin->getNewInstance();
        $this->admin->setSubject($object);

        $form = $this->admin->getForm();
        $form->setData($object);

        if ($this->getRestMethod() == 'POST') {
            $form->submit($this->get('request'));

            $isFormValid = $form->isValid();

            if ($isFormValid && (!$this->isInPreviewMode() || $this->isPreviewApproved())) {
                if (false === $this->admin->isGranted('CREATE', $object)) {
                    throw new AccessDeniedException();
                }

                try {
                    $email = $object->getInvitedEmail();
                    $existUser = $this->getDoctrine()->getRepository('ErpUserBundle:User')
                        ->findOneBy(['email' => $email]);
                    $invitedUsers = $this->getDoctrine()->getRepository('ErpUserBundle:InvitedUser')
                        ->findOneBy(['invitedEmail' => $email]);
                    $property = $this->getDoctrine()->getRepository('ErpPropertyBundle:Property')
                        ->find($object->getProperty()->getId());

                    $em = $this->getDoctrine()->getEntityManager();

                    if (($existUser instanceof User && $existUser->isEnabled()) || $invitedUsers) {
                        $error['message'] = 'Email you are trying to add is either already ';
                        $error['message'] .= 'linked to other property or disabled. Contact Administrator.';
                    } else {
                        if ($existUser instanceof User
                            && !$existUser->isEnabled()
                            && $existUser->hasRole(User::ROLE_TENANT)
                        ) {
                            $userService = $this->get('erp.users.user.service');
                            $userService->activateUser($existUser);
                            $property->setTenantUser($existUser);
                            $this->sendAssignTenantEmail($existUser);
                            $em->persist($property->setStatus(Property::STATUS_RENTED));
                        } elseif ($existUser instanceof User
                            && !$existUser->isEnabled()
                            && $existUser->hasRole(User::ROLE_MANAGER)
                        ) {
                            $error['message'] = 'Email is disabled. Contact Administrator.';
                        } else {
                            $object
                                ->setInviteCode($this->get('fos_user.util.token_generator')->generateToken())
                                ->setIsUse(false);
                            $object = $this->admin->create($object);
                            $this->sendInviteTenantEmail($object);
                            $em->persist($property->setStatus(Property::STATUS_RENTED));
                        }

                        $em->flush();
                    }

                    if (!$error) {
                        if ($this->isXmlHttpRequest()) {
                            return $this->renderJson(
                                array(
                                    'result'   => 'ok',
                                    'objectId' => $this->admin->getNormalizedIdentifier($object),
                                )
                            );
                        }

                        $this->addFlash(
                            'sonata_flash_success',
                            $this->admin->trans(
                                'flash_create_success',
                                array('%name%' => $this->escapeHtml($this->admin->toString($object))),
                                'SonataAdminBundle'
                            )
                        );

                        return $this->redirectTo($object);
                    }
                } catch (ModelManagerException $e) {
                    $isFormValid = false;
                }
            }

            if (!$isFormValid || $error) {
                if (!$this->isXmlHttpRequest()) {
                    $error['message'] = ($error)
                        ? $error['message']
                        : $this->admin->trans(
                            'flash_create_error',
                            ['%name%' => $this->escapeHtml($this->admin->toString($object))],
                            'SonataAdminBundle'
                        );

                    $this->addFlash(
                        'sonata_flash_error',
                        $error['message']
                    );
                }
            } elseif ($this->isPreviewRequested()) {
                $templateKey = 'preview';
                $this->admin->getShow();
            }
        }

        $view = $form->createView();
        $this->get('twig')->getExtension('form')->renderer->setTheme($view, $this->admin->getFormTheme());

        return $this->render(
            $this->admin->getTemplate($templateKey),
            ['action' => 'create', 'form' => $view, 'object' => $object]
        );
    }

    /**
     * Send invite tenant email
     *
     * @param InvitedUser $invitedUser
     *
     * @return bool
     */
    protected function sendInviteTenantEmail(InvitedUser $invitedUser)
    {
        $url = $this->generateUrl(
            'erp_user_tenant_registration',
            ['token' => $invitedUser->getInviteCode()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $user = $this->getUser();
        $emailParams = [
            'sendTo'        => $invitedUser->getInvitedEmail(),
            'mailFromTitle' => $user->getFromForEmail(),
            'preSubject'    => $user->getSubjectForEmail(),
            'url'           => $url,
            'invitedUser'   => $invitedUser
        ];

        $sentStatus = $this->get('erp.core.email_notification.service')
            ->sendEmail(EmailNotificationFactory::TYPE_INVITE_TENANT_USER, $emailParams);

        return $sentStatus;
    }

    /**
     * Send assign tenant email
     *
     * @param object $user
     *
     * @return bool
     */
    protected function sendAssignTenantEmail($user)
    {
        $url = $this->generateUrl(
            'fos_user_security_login',
            [],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $emailParams = [
            'sendTo' => $user->getEmail(),
            'url'    => $url,
        ];

        $sentStatus = $this->get('erp.core.email_notification.service')
            ->sendEmail(EmailNotificationFactory::TYPE_ASSIGN_TENANT_USER, $emailParams);

        return $sentStatus;
    }
}
