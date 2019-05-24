<?php

namespace Erp\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ContactPageRequest
 *
 * @ORM\Table(name="contact_page_requests")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class ContactPageRequest {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     *
     * @Assert\NotBlank(message="Please fill out the field", groups={"ContactPageRequest"})
     *
     * @Assert\Length(
     *     min=2,
     *     max=255,
     *     minMessage="Name should have minimum 2 characters and maximum 255 characters",
     *     maxMessage="Name should have minimum 2 characters and maximum 255 characters",
     *     groups={"ContactPageRequest"}
     * )
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="surname", type="string", length=255)
     *
     * @Assert\NotBlank(message="Please fill out the field", groups={"ContactPageRequest"})
     *
     * @Assert\Length(
     *     min=2,
     *     max=255,
     *     minMessage="Name should have minimum 2 characters and maximum 255 characters",
     *     maxMessage="Name should have minimum 2 characters and maximum 255 characters",
     *     groups={"ContactPageRequest"}
     * )
     */
    protected $surname;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     *
     * @Assert\NotBlank(message="Please fill out the field", groups={"ContactPageRequest"})
     *
     * @Assert\Email(
     *     groups={"ContactPageRequest"}
     * )
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     *
     * @Assert\Regex(
     *     pattern="/^([01][- .])?(\(\d{3}\)|\d{3})[- .]?\d{3}[- .]\d{4}$/i",
     *     htmlPattern="^([01][- .])?(\(\d{3}\)|\d{3})[- .]?\d{3}[- .]\d{4}$",
     *     message=" Enter phone in one of the following formats: (555)555-5555 OR 555-555-5555",
     *     groups={"ContactPageRequest"}
     * )
     */
    protected $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=255)
     *
     * @Assert\NotBlank(message="Please fill out the field", groups={"ContactPageRequest"})
     *
     * @Assert\Length(
     *     min=2,
     *     max=255,
     *     minMessage="Subject should have minimum 2 characters and maximum 255 characters",
     *     maxMessage="Subject should have minimum 2 characters and maximum 255 characters",
     *     groups={"ContactPageRequest"}
     * )
     */
    protected $subject;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text")
     *
     * @Assert\NotBlank(message="Please fill out the field", groups={"ContactPageRequest"})
     *
     * @Assert\Length(
     *     min=1,
     *     max=255,
     *     minMessage="Message should have minimum 1 characters and maximum 255 characters",
     *     maxMessage="Message should have minimum 1 characters and maximum 255 characters",
     *     groups={"ContactPageRequest"}
     * )
     */
    protected $message;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_date", type="datetime")
     */
    protected $createdDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_date", type="datetime")
     */
    protected $updatedDate;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return ContentPageRequest
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set surname
     *
     * @param string $surname
     *
     * @return ContentPageRequest
     */
    public function setSurname($surname) {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get surname
     *
     * @return string
     */
    public function getSurname() {
        return $this->surname;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return ContentPageRequest
     */
    public function setEmail($email) {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return ContentPageRequest
     */
    public function setPhone($phone) {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone() {
        return $this->phone;
    }

    /**
     * Set subject
     *
     * @param string $subject
     *
     * @return ContentPageRequest
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
     * Set message
     *
     * @param string $message
     *
     * @return ContentPageRequest
     */
    public function setMessage($message) {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * Set createdDate
     *
     * @ORM\PrePersist
     *
     * @return $this
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
     * Set updatedDate
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     *
     * @return $this
     */
    public function setUpdatedDate() {
        $this->updatedDate = new \DateTime();
    }

    /**
     * Get updatedDate
     *
     * @return \DateTime
     */
    public function getUpdatedDate() {
        return $this->updatedDate;
    }

}
