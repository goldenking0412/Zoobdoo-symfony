<?php

namespace Erp\PropertyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Erp\PropertyBundle\Entity\Property;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * AppointmentRequest
 *
 * @ORM\Table(name="appointment_requests")
 * @ORM\Entity(repositoryClass="Erp\PropertyBundle\Repository\AppointmentRequestRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class AppointmentRequest
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Property
     *
     * @ORM\ManyToOne(targetEntity="\Erp\PropertyBundle\Entity\Property", inversedBy="appointmentRequests")
     * @ORM\JoinColumn(name="property_id", referencedColumnName="id")
     */
    protected $property;

    /**
     * @var string
     *
     * @ORM\Column(name="user_name", type="string", length=255)
     *
     * @Assert\NotBlank(message="Please enter your Name", groups={"RequestCreated"})
     *
     * @Assert\Length(
     *     min=2,
     *     max=255,
     *     minMessage="Name should have minimum 2 characters and maximum 255 characters",
     *     maxMessage="Name should have minimum 2 characters and maximum 255 characters",
     *     groups={"RequestCreated"}
     * )
     */
    protected $userName;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     *
     * @Assert\Regex(
     *     pattern="/^([01][- .])?(\(\d{3}\)|\d{3})[- .]?\d{3}[- .]\d{4}$/i",
     *     htmlPattern="^([01][- .])?(\(\d{3}\)|\d{3})[- .]?\d{3}[- .]\d{4}$",
     *     message=" Enter phone in one of the following formats: (555)555-5555 OR 555-555-5555",
     *     groups={"RequestCreated"}
     * )
     */
    protected $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     *
     * @Assert\NotBlank(message="Please enter Email", groups={"RequestCreated"})
     *
     * @Assert\Length(
     *     min=6,
     *     max=255,
     *     minMessage="Email should have minimum 6 and maximum 255 characters This value is not a valid Email address.
     *          Use following format: example@address.com",
     *     maxMessage="Email should have minimum 6 and maximum 255 characters This value is not a valid Email address.
     *          Use following format: example@address.com",
     *     groups={"RequestCreated"}
     * )
     *
     * @Assert\Email(message="This value is not a valid Email address. Use following formats: example@address.com",
     *              groups={"RequestCreated"})
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=255)
     *
     * @Assert\NotBlank(message="Please enter Subject", groups={"RequestCreated"})
     *
     * @Assert\Length(
     *     min=2,
     *     max=255,
     *     minMessage="Subject should have minimum 2 characters and maximum 255 characters",
     *     maxMessage="Subject should have minimum 2 characters and maximum 255 characters",
     *     groups={"RequestCreated"}
     * )
     */
    protected $subject;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text", nullable=true)
     *
     * @Assert\NotBlank(message="Please enter Message", groups={"RequestCreated"})
     *
     * @Assert\Length(
     *     min=1,
     *     max=255,
     *     minMessage="Message should have minimum 1 and maximum 255 characters",
     *     maxMessage="Message should have minimum 1 and maximum 255 characters",
     *     groups={"RequestCreated"}
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
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set property
     *
     * @param Property $property
     *
     * @return AppointmentRequest
     */
    public function setProperty(Property $property)
    {
        $this->property = $property;

        return $this;
    }

    /**
     * Get property
     *
     * @return Property
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * Set userName
     *
     * @param string $userName
     *
     * @return AppointmentRequest
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;

        return $this;
    }

    /**
     * Get userName
     *
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return AppointmentRequest
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return AppointmentRequest
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set subject
     *
     * @param string $subject
     *
     * @return AppointmentRequest
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set message
     *
     * @param string $message
     *
     * @return AppointmentRequest
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set createdDate
     *
     * @ORM\PrePersist
     *
     * @return AppointmentRequest
     */
    public function setCreatedDate()
    {
        $this->createdDate = new \DateTime();
    }

    /**
     * Get createdDate
     *
     * @return \DateTime
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * Set updatedDate
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     *
     * @return AppointmentRequest
     */
    public function setUpdatedDate()
    {
        $this->updatedDate = new \DateTime();
    }

    /**
     * Get updatedDate
     *
     * @return \DateTime
     */
    public function getUpdatedDate()
    {
        return $this->updatedDate;
    }
}
