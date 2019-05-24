<?php

namespace Erp\NotificationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Erp\CoreBundle\Entity\DatesAwareTrait;
use Erp\CoreBundle\Entity\DatesAwareInterface;
use Erp\UserBundle\Entity\User;
use Erp\NotificationBundle\Entity\Template;
use Erp\PropertyBundle\Entity\Property;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * EvictionData
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class EvictionData implements DatesAwareInterface {

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
     * @var string
     *
     * @ORM\Column(name="tracking_info", type="text")
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="pick_days", type="text")
     */
    private $days;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="\Erp\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var Property
     *
     * @ORM\ManyToOne(targetEntity="\Erp\PropertyBundle\Entity\Property")
     * @ORM\JoinColumn(name="property_id", referencedColumnName="id")
     */
    private $properties;

    /**
     * @var Template
     *
     * @ORM\ManyToOne(targetEntity="\Erp\NotificationBundle\Entity\Template")
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id")
     */
    private $template;

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
     * Set user
     *
     * @param \Erp\UserBundle\Entity\User $user
     *
     * @return Template
     */
    public function setUser(\Erp\UserBundle\Entity\User $user = null) {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Erp\UserBundle\Entity\User
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * Add property
     *
     * @param \Erp\PropertyBundle\Entity\Property $property
     *
     * @return UserNotification
     */
    public function addProperty(\Erp\PropertyBundle\Entity\Property $property) {
        $this->properties = $property;

        return $this;
    }

    /**
     * Get properties
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProperties() {
        return $this->properties;
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
     * Set description
     *
     * @param string $description
     *
     * @return string
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Set days
     *
     * @param string $description
     *
     * @return string
     */
    public function setDays($days) {
        $this->days = $days;

        return $this;
    }

    /**
     * Get days
     *
     * @return string
     */
    public function getDays() {
        return $this->days;
    }

}
