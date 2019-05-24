<?php

namespace Erp\CoreBundle\Listener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\Event;
use Erp\CoreBundle\EmailNotification\EmailNotificationFactory;
use Erp\UserBundle\Entity\User;
use Erp\CoreBundle\Entity\EmailNotification;

/**
 * Class EmailNotificationListener
 *
 * @package Erp\CoreBundle\Listener
 */
class EmailNotificationListener
{
    protected $container;

    /**
     * EmailNotificationListener constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Listener: Send Email notification
     *
     * @param Event $event
     *
     * @return bool
     */
    public function sendEmailNotification(Event $event)
    {
        /** @var User $user */
        $user = $event->getUser();
        $emailNotificationType = $event->getEmailNotificationType();
        $emailNotificationTokens = $event->getEmailNotificationTokens();

        $emailNotificationRepository = $this->container->get('doctrine')
            ->getRepository('ErpCoreBundle:EmailNotification');
        $emailNotification = $emailNotificationRepository->findOneBy(['type' => strtoupper($emailNotificationType)]);

        if (!$emailNotification instanceof EmailNotification) {
            return false;
        }

        if (!$user->checkSetting($emailNotificationType)) {
            return false;
        }

        $emailParams = [
            'sendTo' => $user->getSecondEmail() ? $user->getSecondEmail() : $user->getEmail(),
            'subject' => $emailNotification->getSubject(),
            'title' => $emailNotification->getTitle(),
            'button' => $emailNotification->getButton(),
            'tokens' => $emailNotificationTokens,
            'body' =>
                str_replace($emailNotification->getTokens(), $emailNotificationTokens, $emailNotification->getBody()),
        ];

        $emailType = EmailNotificationFactory::TYPE_USER_SETTING;
        $sentStatus = $this->container->get('erp.core.email_notification.service')->sendEmail($emailType, $emailParams);

        return $sentStatus;
    }
}
