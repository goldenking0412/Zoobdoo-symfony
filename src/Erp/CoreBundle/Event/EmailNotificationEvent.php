<?php

namespace Erp\CoreBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Erp\UserBundle\Entity\User;

/**
 * Class EmailNotificationEvent
 *
 * @package Erp\CoreBundle\Event
 */
class EmailNotificationEvent extends Event
{
    protected $user;

    protected $emailNotificationType;

    protected $emailNotificationTokens = [];

    /**
     * EmailNotificationEvent constructor.
     *
     * @param User   $user
     * @param string $emailNotificationType
     * @param array  $params
     */
    public function __construct(User $user, $emailNotificationType, $emailNotificationTokens = [])
    {
        $this->user = $user;
        $this->emailNotificationType = $emailNotificationType;
        $this->emailNotificationTokens = $emailNotificationTokens;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getEmailNotificationType()
    {
        return $this->emailNotificationType;
    }

    /**
     * @return array
     */
    public function getEmailNotificationTokens()
    {
        return $this->emailNotificationTokens;
    }
}
