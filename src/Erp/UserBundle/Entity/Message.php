<?php

namespace Erp\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Erp\UserBundle\Entity\User;

/**
 * Message
 *
 * @ORM\Table(name="messages")
 * @ORM\Entity(repositoryClass="Erp\UserBundle\Repository\MessageRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Message {
    
    const REPOSITORY = 'ErpUserBundle:Message';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(
     *      targetEntity="\Erp\UserBundle\Entity\User"
     * )
     * @ORM\JoinColumn(
     *      name="from_user_id",
     *      referencedColumnName="id",
     *      onDelete="CASCADE"
     * )
     */
    protected $fromUser;

    /**
     * @ORM\ManyToOne(
     *      targetEntity="\Erp\UserBundle\Entity\User"
     * )
     * @ORM\JoinColumn(
     *      name="to_user_id",
     *      referencedColumnName="id",
     *      onDelete="CASCADE"
     * )
     */
    protected $toUser;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=255, nullable=true)
     *
     * @Assert\Length(
     *     max=255,
     *     maxMessage=" Subject should have minimum 1 and maximum 255 characters",
     *     groups={"Messages"}
     * )
     */
    protected $subject;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text")
     *
     * @Assert\NotBlank(
     *     message="Please enter text",
     *     groups={"Messages"}
     * )
     *
     * @Assert\Length(
     *     min=1,
     *     max=255,
     *     minMessage="Message should have minimum 1 and maximum 255 characters",
     *     maxMessage="Message should have minimum 1 and maximum 255 characters",
     *     groups={"Messages"}
     * )
     */
    protected $text;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_date", type="datetime")
     */
    protected $createdDate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_read", type="boolean")
     */
    protected $isRead = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="send_sms", type="boolean", nullable=true)
     */
    protected $sendSms = false;

    /**
     * @var string
     *
     * @ORM\Column(name="from_number", type="string", length=255, nullable=true)
     */
    protected $fromNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="to_number", type="string", length=255, nullable=true)
     */
    protected $toNumber;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set subject
     *
     * @param string $subject
     *
     * @return Message
     */
    public function setSubject($subject) {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject() {
        return $this->subject;
    }

    /**
     * Set text
     *
     * @param string $text
     *
     * @return Message
     */
    public function setText($text) {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText() {
        return $this->text;
    }

    /**
     * Set createdDate
     *
     * @ORM\PrePersist
     */
    public function setCreatedDate() {
        $this->createdDate = new \DateTime();
    }

    /**
     * Get createdDate
     *
     * @return \DateTime
     */
    public function getCreatedDate() {
        return $this->createdDate;
    }

    /**
     * Set isRead
     *
     * @param boolean $isRead
     *
     * @return Message
     */
    public function setIsRead($isRead) {
        $this->isRead = $isRead;

        return $this;
    }

    /**
     * Get isRead
     *
     * @return boolean
     */
    public function getIsRead() {
        return $this->isRead;
    }

    /**
     * Set sendSms
     *
     * @param boolean $sendSms
     *
     * @return Message
     */
    public function setSendSms($sendSms) {
        $this->sendSms = $sendSms;

        return $this;
    }

    /**
     * Get sendSms
     *
     * @return boolean
     */
    public function getSendSms() {
        return $this->sendSms;
    }

    /**
     * Set fromUser
     *
     * @param User $fromUser
     *
     * @return Message
     */
    public function setFromUser(User $fromUser = null) {
        $this->fromUser = $fromUser;

        return $this;
    }

    /**
     * Get fromUser
     *
     * @return User
     */
    public function getFromUser() {
        return $this->fromUser;
    }

    /**
     * Set toUser
     *
     * @param User $toUser
     *
     * @return Message
     */
    public function setToUser(User $toUser = null) {
        $this->toUser = $toUser;

        return $this;
    }

    /**
     * Get toUser
     *
     * @return User
     */
    public function getToUser() {
        return $this->toUser;
    }

    /**
     * Set fromNumber
     *
     * @param string $fromNumber
     *
     * @return Message
     */
    public function setFromNumber($fromNumber) {
        $this->fromNumber = $fromNumber;

        return $this;
    }

    /**
     * Get fromNumber
     *
     * @return string
     */
    public function getFromNumber() {
        return $this->fromNumber;
    }

    /**
     * Set toNumber
     *
     * @param string $toNumber
     *
     * @return Message
     */
    public function setToNumber($toNumber) {
        $this->toNumber = $toNumber;

        return $this;
    }

    /**
     * Get toNumber
     *
     * @return string
     */
    public function getToNumber() {
        return $this->toNumber;
    }

}
