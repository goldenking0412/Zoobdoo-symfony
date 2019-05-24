<?php

namespace Erp\NotificationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Notification
 *
 * @ORM\Table(name="erp_notification_notification")
 * @ORM\Entity
 */
class Notification
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="days_before", type="integer")
     */
    private $daysBefore;

    /**
     * @ORM\ManyToOne(targetEntity="UserNotification", inversedBy="notifications", cascade={"persist"})
     * @ORM\JoinColumn(name="user_notification_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $userNotification;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set daysBefore
     *
     * @param integer $daysBefore
     *
     * @return Notification
     */
    public function setDaysBefore($daysBefore)
    {
        $this->daysBefore = $daysBefore;

        return $this;
    }

    /**
     * Get daysBefore
     *
     * @return integer
     */
    public function getDaysBefore()
    {
        return $this->daysBefore;
    }

    /**
     * Set userNotification
     *
     * @param \Erp\NotificationBundle\Entity\UserNotification $userNotification
     *
     * @return Notification
     */
    public function setUserNotification(\Erp\NotificationBundle\Entity\UserNotification $userNotification = null)
    {
        $this->userNotification = $userNotification;

        return $this;
    }

    /**
     * Get userNotification
     *
     * @return \Erp\NotificationBundle\Entity\UserNotification
     */
    public function getUserNotification()
    {
        return $this->userNotification;
    }
}
