<?php

namespace Erp\NotificationBundle\Entity;

use Erp\CoreBundle\Entity\DatesAwareTrait;
use Erp\CoreBundle\Entity\DatesAwareInterface;
use Doctrine\ORM\Mapping as ORM;
use Erp\PropertyBundle\Entity\Property;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class UserNotification
 *
 * @ORM\Table(name="erp_notification_user_notification")
 * @ORM\Entity(repositoryClass="Erp\NotificationBundle\Repository\UserNotificationRepository")
 * @ORM\HasLifecycleCallbacks
 */
class UserNotification implements DatesAwareInterface {

    use DatesAwareTrait;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Notification", mappedBy="userNotification", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $notifications;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Alert", mappedBy="userNotification", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $alerts;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_send_alert_automatically", type="boolean")
     */
    private $sendAlertAutomatically;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_send_notification_automatically", type="boolean")
     */
    private $sendNotificationAutomatically;

    /**
     * @var boolean
     *
     * @ORM\Column(name="send_sms", type="boolean")
     */
    private $sendSms;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Erp\PropertyBundle\Entity\Property")
     * @ORM\JoinTable(
     *     name="erp_notification_user_notification_property",
     *     joinColumns={@ORM\JoinColumn(name="user_notification_id", referencedColumnName="id", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="property_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    private $properties;

    /**
     * @var Template
     *
     * @ORM\ManyToOne(targetEntity="Template")
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $template;

    /**
     * @var Template
     *
     * @ORM\ManyToOne(targetEntity="Template")
     * @ORM\JoinColumn(name="template_sms_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $templateSms;

    public function __construct() {
        $this->notifications = new ArrayCollection();
        $this->alerts = new ArrayCollection();
        $this->properties = new ArrayCollection();
        $this->sendAlertAutomatically = false;
        $this->sendNotificationAutomatically = false;
        $this->sendSms = false;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist() {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function setUpdatedDate() {
        $this->updatedAt = new \DateTime();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set sendAlertAutomatically
     *
     * @param integer $sendAlertAutomatically
     *
     * @return UserNotification
     */
    public function setSendAlertAutomatically($sendAlertAutomatically) {
        $this->sendAlertAutomatically = $sendAlertAutomatically;

        return $this;
    }

    /**
     * Get sendAlertAutomatically
     *
     * @return integer
     */
    public function getSendAlertAutomatically() {
        return $this->sendAlertAutomatically;
    }

    /**
     * Set sendNotificationAutomatically
     *
     * @param integer $sendNotificationAutomatically
     *
     * @return UserNotification
     */
    public function setSendNotificationAutomatically($sendNotificationAutomatically) {
        $this->sendNotificationAutomatically = $sendNotificationAutomatically;

        return $this;
    }

    /**
     * Get sendNotificationAutomatically
     *
     * @return integer
     */
    public function getSendNotificationAutomatically() {
        return $this->sendNotificationAutomatically;
    }

    /**
     * Set sendSms
     *
     * @param integer $sendSms
     *
     * @return UserNotification
     */
    public function setSendSms($sendSms) {
        $this->sendSms = $sendSms;

        return $this;
    }

    /**
     * Get sendSms
     *
     * @return integer
     */
    public function getSendSms() {
        return $this->sendSms;
    }

    /**
     * Add notification
     *
     * @param \Erp\NotificationBundle\Entity\Notification $notification
     *
     * @return UserNotification
     */
    public function addNotification(\Erp\NotificationBundle\Entity\Notification $notification) {
        $notification->setUserNotification($this);
        $this->notifications[] = $notification;

        return $this;
    }

    /**
     * Remove notification
     *
     * @param \Erp\NotificationBundle\Entity\Notification $notification
     */
    public function removeNotification(\Erp\NotificationBundle\Entity\Notification $notification) {
        $this->notifications->removeElement($notification);
    }

    /**
     * Get notifications
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNotifications() {
        return $this->notifications;
    }

    /**
     * Add alert
     *
     * @param \Erp\NotificationBundle\Entity\Alert $alert
     *
     * @return UserNotification
     */
    public function addAlert(\Erp\NotificationBundle\Entity\Alert $alert) {
        $alert->setUserNotification($this);
        $this->alerts[] = $alert;

        return $this;
    }

    /**
     * Remove alert
     *
     * @param \Erp\NotificationBundle\Entity\Alert $alert
     */
    public function removeAlert(\Erp\NotificationBundle\Entity\Alert $alert) {
        $this->alerts->removeElement($alert);
    }

    /**
     * Get alerts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAlerts() {
        return $this->alerts;
    }

    /**
     * Add property
     *
     * @param \Erp\PropertyBundle\Entity\Property $property
     *
     * @return UserNotification
     */
    public function addProperty(\Erp\PropertyBundle\Entity\Property $property) {
        $this->properties[] = $property;

        return $this;
    }

    /**
     * Remove property
     *
     * @param \Erp\PropertyBundle\Entity\Property $property
     */
    public function removeProperty(\Erp\PropertyBundle\Entity\Property $property) {
        $this->properties->removeElement($property);
    }

    /**
     * Get properties
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProperties() {
        return $this->properties;
    }

    public function eraseProperties() {
        $this->properties = new ArrayCollection();
        return $this;
    }

    /**
     * Set template
     *
     * @param \Erp\NotificationBundle\Entity\Template $template
     *
     * @return UserNotification
     */
    public function setTemplate(\Erp\NotificationBundle\Entity\Template $template = null) {
        $this->template = $template;

        return $this;
    }

    /**
     * Get template
     *
     * @return \Erp\NotificationBundle\Entity\Template
     */
    public function getTemplate() {

        return $this->template;
    }

    /**
     * Set templateSms
     *
     * @param \Erp\NotificationBundle\Entity\Template $templateSms
     *
     * @return UserNotification
     */
    public function setTemplateSms(\Erp\NotificationBundle\Entity\Template $templateSms = null) {
        $this->templateSms = $templateSms;

        return $this;
    }

    /**
     * Get templateSms
     *
     * @return \Erp\NotificationBundle\Entity\Template
     */
    public function getTemplateSms() {
        return $this->templateSms;
    }

}
