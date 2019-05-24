<?php

namespace Erp\UserBundle\Services;

use Erp\CoreBundle\EmailNotification\EmailNotificationFactory;
use Erp\CoreBundle\Entity\EmailNotification;
use Erp\UserBundle\Entity\User;
use Erp\UserBundle\Entity\Message;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UserService {

    const CACHE_KEY_CUTOMERS_INFO = 'ps_customers';

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var \PHY\CacheBundle\Cache
     */
    protected $apcCache;

    /**
     * @var \Erp\CoreBundle\Services\Logger
     */
    protected $logger;

    /**
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container = null) {
        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getManager();
        $this->logger = $this->container->get('erp.logger');
    }

    /**
     * Get service request types
     *
     * @return array
     */
    public function getServiceRequestTypes() {
        return [
            1 => 'Plumbing',
            2 => 'Electrical',
            3 => 'Heating/Cooling',
            4 => 'Cosmetic',
            5 => 'Other'
        ];
    }

    /**
     * Return settings (email notifications)
     *
     * @return array
     */
    public function getSettings() {
        $emailNotifications = $this->em->getRepository('ErpCoreBundle:EmailNotification')->findAll();

        if (!$emailNotifications) {
            throw new NotFoundHttpException('Email notifications not found');
        }

        /** @var EmailNotification $emailNotification */
        foreach ($emailNotifications as $emailNotification) {
            $result[$emailNotification->getType()] = $emailNotification->getName();
        }

        return $result;
    }

    /**
     * Activate user
     *
     * @param User $user
     */
    public function activateUser(User $user) {
        $this->em->persist($user->setEnabled(true)->setStatus(User::STATUS_ACTIVE));
        $this->em->flush();
    }

    /**
     * Deactivate user
     *
     * @param User $user
     * @param bool $flush
     * @return $this
     */
    public function deactivateUser(User $user, $flush = true, User $initiator = null) {
        $emailParams = [
            'sendTo' => $user->getEmail(),
            'url' => $this->container->get('router')->generate('erp_site_contact_page', [], true),
        ];
        if ($initiator) {
            $emailParams['mailFromTitle'] = $initiator->getFromForEmail();
            $emailParams['preSubject'] = $initiator->getSubjectForEmail();
        }

        $emailType = EmailNotificationFactory::TYPE_USER_DEACTIVATE;
        $this->container->get('erp.core.email_notification.service')->sendEmail($emailType, $emailParams);

        $user
                ->setEnabled(false)
                ->setStatus(User::STATUS_DISABLED);

        $this->em->persist($user);

        if ($flush) {
            $this->em->flush();
        }

        return $this;
    }

    /**
     * Set status for unread messages on read
     *
     * @param User $user
     */
    public function setStatusUnreadMessages(User $user) {
        $messages = $this->em->getRepository('ErpUserBundle:Message')->findBy(['fromUser' => $user, 'isRead' => false]);

        /** @var Message $message */
        foreach ($messages as $message) {
            $message->setIsRead(true);
            $this->em->persist($message);
        }

        $this->em->flush();

        return;
    }

}
