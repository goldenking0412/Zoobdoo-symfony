<?php

namespace Erp\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Erp\CoreBundle\Entity\EmailNotification;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class EmailNotificationFixture extends AbstractFixture implements ContainerAwareInterface {

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager) {
        $this->createEmailNotifications();
    }

    /**
     * @var $emailNotificationManager
     */
    protected $emailNotificationManager;

    public function setContainer(ContainerInterface $container = null) {
        $this->emailNotificationManager = $container->get('app.core.email_notification.manager');
    }

    private function createEmailNotifications() {
        $this->createEmailNotification('SERVICE_REQUESTS', 'Service Request', 'Zoobdoo - New Service Request', '', json_decode('{"0":"#url#"}'), 'Click to see', 'You have received a new service request from Tenant!');
        $this->createEmailNotification('FORUM_TOPICS', 'Forum message', 'Zoobdoo - New Forum Message', '', json_decode('{"0":"#url#"}'), 'Click to see', 'There is a new message on Forum!');
        $this->createEmailNotification('PROFILE_MESSAGES', 'Profile messages', 'Zoobdoo - New Profile Message', '', json_decode('{"0":"#url#"}'), 'Click to see', 'You have received a new message from Tenant.');
    }

    protected function createEmailNotification($type, $name, $subject, $body, $tokens, $button, $title) {
        $notification = new EmailNotification();
        $notification->setType($type);
        $notification->setName($name);
        $notification->setSubject($subject);
        $notification->setBody($body);
        $notification->setTokens($tokens);
        $notification->setButton($button);
        $notification->setTitle($title);

        $emailNotification = $this->emailNotificationManager->update($notification, true);
        return $emailNotification;
    }

}
