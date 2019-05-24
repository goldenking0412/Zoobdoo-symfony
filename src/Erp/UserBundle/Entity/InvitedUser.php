<?php

namespace Erp\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Erp\UserBundle\Entity\User;
use Erp\PropertyBundle\Entity\Property;

/**
 * UserBusiness
 *
 * @ORM\Table(name="invited_users", indexes={@ORM\Index(name="email_index", columns={"invited_email"})})
 * @ORM\Entity(repositoryClass="Erp\UserBundle\Repository\InvitedUserRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class InvitedUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="\Erp\PropertyBundle\Entity\Property", inversedBy="invitedUsers")
     * @ORM\JoinColumn(name="property_id", referencedColumnName="id")
     */
    protected $property;

    /**
     * @var string
     *
     * @ORM\Column(name="invited_email", type="string", length=255, nullable=true)
     *
     * @Assert\NotBlank(message="Please fill out the field", groups={"InvitedUser"})
     * @Assert\Length(
     *     max=255,
     *     maxMessage="Email should have maximum 255 characters",
     *     groups={"InvitedUser"}
     * )
     * @Assert\Email(message="Use following formats: example@address.com", groups={"InvitedUser"})
     */
    protected $invitedEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="invited_code", type="string", length=50, nullable=true)
     */
    protected $inviteCode;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255, nullable=true)
     */
    protected $firstName;
    
    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255, nullable=true)
     */
    protected $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="age", type="integer", length=3, nullable=true)
     */
    protected $age;

    /**
     * @var date
     *
     * @ORM\Column(name="birthdate", type="date", nullable=true)
     */
    protected $birthdate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_use", type="boolean")
     */
    protected $isUse = false;

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
     * Set invitedEmail
     *
     * @param string $invitedEmail
     *
     * @return $this
     */
    public function setInvitedEmail($invitedEmail)
    {
        $this->invitedEmail = $invitedEmail;

        return $this;
    }

    /**
     * Get invitedEmail
     *
     * @return string
     */
    public function getInvitedEmail()
    {
        return $this->invitedEmail;
    }

    /**
     * Set inviteCode
     *
     * @param string $inviteCode
     *
     * @return $this
     */
    public function setInviteCode($inviteCode)
    {
        $this->inviteCode = $inviteCode;

        return $this;
    }

    /**
     * Get inviteCode
     *
     * @return string
     */
    public function getInviteCode()
    {
        return $this->inviteCode;
    }

    /**
     * Set isUse
     *
     * @param boolean $isUse
     *
     * @return $this
     */
    public function setIsUse($isUse)
    {
        $this->isUse = $isUse;

        return $this;
    }

    /**
     * Get isUse
     *
     * @return boolean
     */
    public function getIsUse()
    {
        return $this->isUse;
    }

    /**
     * Set createdDate
     *
     * @ORM\PrePersist
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

    /**
     * Set property
     *
     * @param Property $property
     *
     * @return InvitedUser
     */
    public function setProperty(Property $property = null)
    {
        $this->property = $property;

        return $this;
    }

    /**
     * Get property
     *
     * @return \Erp\PropertyBundle\Entity\Property
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return $this
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }
    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return $this
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set age
     *
     * @param integer $age
     *
     * @return $this
     */
    public function setAge($age)
    {
        $this->age = $age;

        return $this;
    }

    /**
     * Get age
     *
     * @return integer
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * Set date
     *
     * @param integer $date
     *
     * @return $this
     */
    public function setBirthdate($birthdate)
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    /**
     * Get date
     *
     * @return integer
     */
    public function getBirthdate()
    {
        return $this->birthdate;
    }
}
