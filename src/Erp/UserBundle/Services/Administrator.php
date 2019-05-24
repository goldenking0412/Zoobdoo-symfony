<?php
namespace Erp\UserBundle\Services;

use Erp\CoreBundle\EmailNotification\EmailNotificationFactory;
use Erp\UserBundle\Entity\User;
use FOS\UserBundle\Model\UserInterface;

class Administrator extends UserService
{
    /**
     * Send mail after new Administrator created
     *
     * @param User $user
     *
     * @return $this
     */
    public function onNewAdmin(User $user)
    {
        /** @var $tokenGenerator \FOS\UserBundle\Util\TokenGeneratorInterface */
        $tokenGenerator = $this->container->get('fos_user.util.token_generator');
        $user->setUsername($user->getEmail())
            ->setPassword('')
            ->setConfirmationToken($tokenGenerator->generateToken())
        ->setRoles([User::ROLE_ADMIN])
        ->setStatus(User::STATUS_ACTIVE)
        ->setEnabled(true);

        $this->sendMail($user);

        $user->setPasswordRequestedAt(new \DateTime());
        $this->container->get('fos_user.user_manager')->updateUser($user);

        return $this;
    }

    /**
     * Get admin statuses
     *
     * @return array
     */
    public function getStatuses()
    {
        $statuses = [
            User::STATUS_ACTIVE        => 'Active',
            User::STATUS_DISABLED      => 'Disabled',
        ];

        return $statuses;
    }

    /**
     * @param UserInterface $user
     */
    protected function sendMail(UserInterface $user)
    {
        $url =
            $this->container->get('router')->generate(
                'fos_user_resetting_reset',
                ['token' => $user->getConfirmationToken()],
                true
            );
        $emailParams = [
            'sendTo' => $user->getEmail(),
            'url' => $url
        ];

        $emailType = EmailNotificationFactory::TYPE_ADMIN_USER_CREATE;
        $this->container->get('erp.core.email_notification.service')->sendEmail($emailType, $emailParams);
    }
}
