<?php

namespace Erp\NotificationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Erp\CoreBundle\Entity\DatesAwareInterface;
use Erp\CoreBundle\Entity\DatesAwareTrait;
use Erp\UserBundle\Entity\User;

/**
 * Class Template
 *
 * @ORM\Table(name="erp_notification_template")
 * @ORM\Entity(repositoryClass="Erp\NotificationBundle\Repository\TemplateRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Template implements DatesAwareInterface {
    
    const REPOSITORY = 'ErpNotificationBundle:Template';

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
     * @ORM\Column(name="type", type="string")
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string")
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="\Erp\UserBundle\Entity\User", inversedBy="templates")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="description_sms", type="text", nullable=true)
     */
    private $descriptionSms;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_sms", type="boolean", nullable=false, options={"default" = 0})
     */
    private $isSms = false;

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
    public function preUpdate() {
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
     * Set type
     *
     * @param string $type
     *
     * @return Template
     */
    public function setType($type) {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Template
     */
    public function setTitle($title) {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Template
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
     * Get descriptionSms
     *
     * @return string
     */
    public function getDescriptionSms() {
        return $this->descriptionSms;
    }

    /**
     * Set descriptionSms
     *
     * @param string $descriptionSms
     *
     * @return Template
     */
    public function setDescriptionSms($descriptionSms) {
        $this->descriptionSms = $descriptionSms;

        return $this;
    }

    /**
     * Get isSms
     *
     * @return boolean
     */
    public function getIsSms() {
        return $this->isSms;
    }

    /**
     * Set isSms
     *
     * @param string $isSms
     *
     * @return Template
     */
    public function setIsSms($isSms) {
        $this->isSms = $isSms;

        return $this;
    }

}
