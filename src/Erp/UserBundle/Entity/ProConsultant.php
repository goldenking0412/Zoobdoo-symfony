<?php

namespace Erp\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * ProConsultant
 *
 * @ORM\Table(name="pro_consultants")
 * @ORM\Entity(repositoryClass="Erp\UserBundle\Repository\ProConsultantRepository")
 *
 * @UniqueEntity(
 *     fields={"firstName", "lastName", "email"},
 *     errorPath="email",
 *     message="Consultant with same First Name/Last Name and email already exist.",
 *     groups={"CreateConsultant"}
 * )
 * @ORM\HasLifecycleCallbacks()
 */
class ProConsultant
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
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255)
     *
     * @Assert\NotBlank(message="Please enter First Name", groups={"CreateConsultant"})
     *
     * @Assert\Length(
     *     min=2,
     *     max=255,
     *     minMessage="First Name should have minimum 2 characters and maximum 255 characters",
     *     maxMessage="First Name should have minimum 2 characters and maximum 255 characters",
     *     groups={"CreateConsultant"}
     * )
     */
    protected $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255)
     *
     * @Assert\NotBlank(message="Please enter Last Name", groups={"CreateConsultant"})
     *
     * @Assert\Length(
     *     min=2,
     *     max=255,
     *     minMessage="Last Name should have minimum 2 characters and maximum 255 characters",
     *     maxMessage="Last Name should have minimum 2 characters and maximum 255 characters",
     *     groups={"CreateConsultant"}
     * )
     */
    protected $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     *
     * @Assert\NotBlank(message="Please enter email", groups={"CreateConsultant"})
     *
     * @Assert\Length(
     *     min=6,
     *     max=255,
     *     minMessage="Email should have minimum 6 characters and maximum 255 characters",
     *     maxMessage="Email should have minimum 6 characters and maximum 255 characters",
     *     groups={"CreateConsultant"}
     * )
     *
     * @Assert\Email(message="This value is not a valid Email address. Use following formats: example@address.com",
     *              groups={"CreateConsultant"})
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255)
     *
     * @Assert\NotBlank(
     *     message="Enter phone in one of the following formats: (555)555-5555 OR 555-555-5555",
     *     groups={"CreateConsultant"}
     * )
     *
     * @Assert\Regex(
     *     pattern="/^([01][- .])?(\(\d{3}\)|\d{3})[- .]?\d{3}[- .]\d{4}$/i",
     *     htmlPattern="^([01][- .])?(\(\d{3}\)|\d{3})[- .]?\d{3}[- .]\d{4}$",
     *     message=" Enter phone in one of the following formats: (555)555-5555 OR 555-555-5555",
     *     groups={"CreateConsultant"}
     * )
     */
    protected $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="text", nullable=true)
     *
     * @Assert\Length(
     *     min=3,
     *     max="255",
     *     minMessage="Address should have minimum 3 characters and maximum 255 characters",
     *     maxMessage="Address should have minimum 3 characters and maximum 255 characters",
     *     groups={"CreateConsultant"}
     * )
     */
    protected $address;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdDate", type="datetime")
     */
    protected $createdDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updatedDate", type="datetime")
     */
    protected $updatedDate;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Erp\UserBundle\Entity\ProRequest", mappedBy="proConsultant", cascade={"ALL"})
     * @ORM\OrderBy({"updatedDate"="DESC"})
     */
    protected $proRequests;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Erp\UserBundle\Entity\ProReport", mappedBy="proConsultant", cascade={"ALL"})
     * @ORM\OrderBy({"updatedDate"="DESC"})
     */
    protected $proReports;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->proRequests = new ArrayCollection();
        $this->proReports = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getFullNameWithEmail();
    }

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
     * Set firstName
     *
     * @param string $firstName
     *
     * @return ProConsultant
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
     * @return ProConsultant
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
     * Set email
     *
     * @param string $email
     *
     * @return ProConsultant
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
     * Set phone
     *
     * @param string $phone
     *
     * @return ProConsultant
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
     * Set address
     *
     * @param string $address
     *
     * @return ProConsultant
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
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
     * Add proRequest
     *
     * @param ProRequest $proRequest
     *
     * @return $this
     */
    public function addProRequest(ProRequest $proRequest)
    {
        $this->proRequests[] = $proRequest;

        return $this;
    }

    /**
     * Remove proRequest
     *
     * @param ProRequest $proRequest
     */
    public function removeProRequest(ProRequest $proRequest)
    {
        $this->proRequests->removeElement($proRequest);
    }

    /**
     * Get proRequests
     *
     * @return ArrayCollection
     */
    public function getProRequests()
    {
        return $this->proRequests;
    }

    /**
     * Add proReport
     *
     * @param ProReport $proReport
     *
     * @return $this
     */
    public function addProReport(ProReport $proReport)
    {
        $this->proReports[] = $proReport;

        return $this;
    }

    /**
     * Remove proReport
     *
     * @param ProReport $proReport
     */
    public function removeProReport(ProReport $proReport)
    {
        $this->proReports->removeElement($proReport);
    }

    /**
     * Get proReports
     *
     * @return ArrayCollection
     */
    public function getProReports()
    {
        return $this->proReports;
    }

    /**
     * Get consultant full name with email
     *
     * @return string
     */
    public function getFullNameWithEmail()
    {
        return $this->firstName . ' ' . $this->lastName . ' (' . $this->email . ')';
    }
}
