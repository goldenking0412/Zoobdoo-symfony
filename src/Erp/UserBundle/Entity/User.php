<?php

namespace Erp\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Erp\NotificationBundle\Entity\Template;
use Erp\PaymentBundle\Entity\StripeCustomer;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Erp\PropertyBundle\Entity\Property;
use Erp\CoreBundle\Entity\Image;
use Erp\CoreBundle\Entity\City;
use Erp\UserBundle\Entity\UserDocument;
use Erp\UserBundle\Entity\ForumTopic;
use Erp\UserBundle\Entity\Charge;
use Erp\SmartMoveBundle\Entity\SmartMoveRenter;

/**
 * User
 *
 * @ORM\Table(
 *     name="users"
 * )
 * @ORM\Entity(repositoryClass="Erp\UserBundle\Repository\UserRepository")
 * @UniqueEntity(
 *     fields={"email"},
 *     message="Email is already in use",
 *     groups={"AdminCreated", "ManagerCreated", "ManagerRegister", "ChangeEmail", "LandlordDetails"}
 * )
 * @ORM\HasLifecycleCallbacks()
 */
class User extends BaseUser {

    const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_LANDLORD = 'ROLE_LANDLORD';
    const ROLE_MANAGER = 'ROLE_MANAGER';
    const ROLE_TENANT = 'ROLE_TENANT';
    const ROLE_ANONYMOUS = 'ROLE_ANONYMOUS';
    const ROLE_USER = 'ROLE_USER';
    const STATUS_PENDING = 'pending';
    const STATUS_ACTIVE = 'active';
    const STATUS_NOT_CONFIRMED = 'not_confirmed';
    const STATUS_DISABLED = 'disabled';
    const STATUS_REJECTED = 'rejected';
    const STATUS_DELETED = 'deleted';
    const DEFAULT_PROPERTY_COUNTER = 1;
    const DEFAULT_APPLICATION_FORM_COUNTER = 0;
    const DEFAULT_CONTRACT_FORM_COUNTER = 0;
    const PRIMARY_PAYMENT_TYPE_BANK_ACCOUNT = StripeCustomer::BANK_ACCOUNT;
    const PRIMARY_PAYMENT_TYPE_CREDIT_CARD = StripeCustomer::CREDIT_CARD;
    
    const REPOSITORY = 'ErpUserBundle:User';

    /**
     * @var string
     */
    public static $passwordPattern = '/^(?=.{5,255})(?=.*[a-zA-Z])(?=.*\d)(?=.*[\W])(?!.*\s).*$/';

    /**
     * @var string
     */
    public static $messagePasswordPattern = 'The password must contain letters, numbers and special characters
    (example: &#@%!$) and must not have spaces';

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
     * @ORM\Column(name="second_email", type="string", length=255, nullable=true)
     *
     * @Assert\Email()
     */
    protected $secondEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255, nullable=true)
     *
     * @Assert\NotBlank(
     *     message="Please enter your First Name",
     *     groups={"ManagerRegister", "ManagerDetails", "TenantDetails", "TenantEdit"}
     * )
     *
     * @Assert\Length(
     *     min=2,
     *     max=255,
     *     minMessage="First Name should have minimum 2 characters and maximum 255 characters",
     *     maxMessage="First name should have minimum 2 characters and maximum 255 characters",
     *     groups={"AdminCreated", "ManagerRegister", "ManagerDetails", "TenantDetails", "TenantEdit"}
     * )
     */
    protected $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255, nullable=true)
     *
     * @Assert\NotBlank(
     *     message="Please enter your Last Name",
     *     groups={"ManagerRegister", "ManagerDetails", "TenantDetails", "TenantEdit"}
     * )
     *
     * @Assert\Length(
     *     min=2,
     *     max=255,
     *     minMessage="Last Name should have minimum 2 characters and maximum 255 characters",
     *     maxMessage="Last Name should have minimum 2 characters and maximum 255 characters",
     *     groups={"AdminCreated", "ManagerRegister", "ManagerDetails", "TenantDetails", "TenantEdit"}
     * )
     */
    protected $lastName;

    /**
     * @var string
     *
     * @Assert\Length(
     *     min=5,
     *     max=255,
     *     minMessage="Password should have minimum 5 characters and maximum 255 characters",
     *     maxMessage="Password should have minimum 5 characters and maximum 255 characters",
     *     groups={"AdminCreated", "ManagerRegister", "ManagerDetails", "TenantDetails", "ResetPassword"}
     * )
     */
    protected $plainPassword;

    /**
     * @var string
     *
     * @ORM\Column(name="company_name", type="string", length=255, nullable=true)
     *
     * @Assert\Length(
     *     min=2,
     *     max=255,
     *     minMessage="Company name should have minimum 2 characters and maximum 255 characters",
     *     maxMessage="Company name should have minimum 2 characters and maximum 255 characters",
     *     groups={"ManagerCreated", "ManagerRegister"}
     * )
     */
    protected $companyName;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=20, nullable=true)
     *
     * @Assert\NotBlank(
     *     message="Please enter Phone number",
     *     groups={"ManagerCreated", "ManagerRegister", "AddressDetails", "TenantContactInfo", "LandlordDetails", "TenantEdit"}
     * )
     *
     * @Assert\Regex(
     *     pattern="/^([01][- .])?(\(\d{3}\)|\d{3})[- .]?\d{3}[- .]\d{4}$/i",
     *     htmlPattern="^([01][- .])?(\(\d{3}\)|\d{3})[- .]?\d{3}[- .]\d{4}$",
     *     message=" Enter phone in one of the following formats: (555)555-5555 OR 555-555-5555",
     *     groups={"ManagerCreated", "ManagerRegister", "AddressDetails", "TenantContactInfo", "LandlordDetails", "TenantEdit"}
     * )
     */
    protected $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="work_phone", type="string", length=20, nullable=true)
     *
     * @Assert\Regex(
     *     pattern="/^([01][- .])?(\(\d{3}\)|\d{3})[- .]?\d{3}[- .]\d{4}$/i",
     *     htmlPattern="^([01][- .])?(\(\d{3}\)|\d{3})[- .]?\d{3}[- .]\d{4}$",
     *     message=" Enter phone in one of the following formats: (555)555-5555 OR 555-555-5555",
     *     groups={"ManagerCreated", "TenantContactInfo", "LandlordDetails", "TenantEdit"}
     * )
     */
    protected $workPhone;

    /**
     * @var string
     *
     * @ORM\Column(name="website_url", type="string", length=255, nullable=true)
     *
     * @Assert\Url(
     *     message="Please use link format. Example: http(s)://mysite.com",
     *     groups={"ManagerCreated", "ManagerRegister", "AddressDetails", "TenantContactInfo", "TenantEdit"}
     * )
     * @Assert\Length(
     *     min=4,
     *     max="255",
     *     minMessage="Website should have minimum 4 characters and maximum 255 characters",
     *     maxMessage="Website should have minimum 4 characters and maximum 255 characters",
     *     groups={"ManagerCreated", "ManagerRegister", "AddressDetails", "TenantContactInfo", "TenantEdit"}
     * )
     */
    protected $websiteUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="address_one", type="string", length=255, nullable=true)
     *
     * @Assert\NotBlank(
     *     message="Please enter your Address",
     *     groups={"ManagerCreated", "ManagerRegister", "AddressDetails", "TenantContactInfo", "TenantEdit"}
     * )
     * @Assert\Length(
     *     min=3,
     *     max="255",
     *     minMessage="Address should have minimum 3 characters and maximum 255 characters",
     *     maxMessage="Address should have minimum 3 characters and maximum 255 characters",
     *     groups={"ManagerCreated", "ManagerRegister", "AddressDetails", "TenantContactInfo", "TenantEdit"}
     * )
     */
    protected $addressOne;

    /**
     * @var string
     *
     * @ORM\Column(name="address_two", type="string", length=255, nullable=true)
     *
     * @Assert\Length(
     *     min=3,
     *     max="255",
     *     minMessage="Address should have minimum 3 characters and maximum 255 characters",
     *     maxMessage="Address should have minimum 3 characters and maximum 255 characters",
     *     groups={"ManagerCreated", "ManagerRegister", "AddressDetails", "TenantContactInfo", "TenantEdit"}
     * )
     */
    protected $addressTwo;

    /**
     * @ORM\ManyToOne(
     *      targetEntity="\Erp\CoreBundle\Entity\City",
     *      inversedBy="users"
     * )
     * @ORM\JoinColumn(
     *      name="city_id",
     *      referencedColumnName="id",
     *      onDelete="CASCADE"
     * )
     * @Assert\NotBlank(
     *     message="Please enter your City",
     *     groups={"ManagerCreated", "ManagerRegister", "AddressDetails", "TenantContactInfo", "TenantEdit"}
     * )
     */
    protected $city;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=35, nullable=true)
     *
     * @Assert\NotBlank(
     *     message="Please enter your State",
     *     groups={"ManagerCreated", "ManagerRegister", "AddressDetails", "TenantContactInfo", "TenantEdit"}
     * )
     * @Assert\Length(
     *     min=2,
     *     max="35",
     *     minMessage="State should have minimum 2 characters and maximum 35 characters",
     *     maxMessage="State should have minimum 2 characters and maximum 35 characters",
     *     groups={"ManagerCreated", "ManagerRegister", "AddressDetails", "TenantContactInfo", "TenantEdit"}
     * )
     */
    protected $state;

    /**
     * @var string
     *
     * @ORM\Column(name="postal_code", type="string", length=35, nullable=true)
     * @Assert\NotBlank(
     *     message="Please enter your Zip Code",
     *     groups={"ManagerCreated", "ManagerRegister", "AddressDetails", "TenantContactInfo", "TenantEdit"}
     * )
     *
     * @Assert\Regex(
     *     pattern="/^[0-9]+$/",
     *     match=true,
     *     message="Zip code must contain numbers",
     *     groups={"ManagerCreated", "ManagerRegister", "AddressDetails", "TenantContactInfo", "TenantEdit"}
     * )
     * @Assert\Length(
     *     min=5,
     *     max=5,
     *     minMessage="Zip code should have minimum 5 characters and maximum 5 characters",
     *     maxMessage="Zip code should have minimum 5 characters and maximum 5 characters",
     *     groups={"ManagerCreated", "ManagerRegister", "AddressDetails", "TenantContactInfo", "TenantEdit"}
     * )
     */
    protected $postalCode;

    /**
     * @var string
     *
     * @ORM\Column(
     *      name="status",
     *      length=16,
     *      type="string",
     *      nullable=true
     * )
     */
    protected $status = self::STATUS_NOT_CONFIRMED;

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
     * @var integer
     *
     * @ORM\Column(name="property_counter", type="integer", nullable=true, options={"default" = 1})
     */
    protected $propertyCounter;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_property_counter_free", type="boolean")
     */
    protected $isPropertyCounterFree = false;

    /**
     * @var integer
     *
     * @ORM\Column(name="application_form_counter", type="integer", nullable=true, options={"default" = 0})
     */
    protected $applicationFormCounter;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_application_form_counter_free", type="boolean")
     */
    protected $isApplicationFormCounterFree = false;

    /**
     * @var integer
     *
     * @ORM\Column(name="contract_form_counter", type="integer", nullable=true, options={"default" = 0})
     */
    protected $contractFormCounter;

    /**
     * @ORM\OneToOne(
     *      targetEntity="\Erp\CoreBundle\Entity\Image",
     *      cascade={"persist"},
     *      orphanRemoval=true
     * )
     *
     * @ORM\JoinColumn(
     *      name="image_id",
     *      referencedColumnName="id"
     * )
     */
    protected $image;

    /**
     * @ORM\OneToOne(
     *      targetEntity="\Erp\CoreBundle\Entity\Image",
     *      cascade={"persist"},
     *      orphanRemoval=true
     * )
     *
     * @ORM\JoinColumn(
     *      name="logo_id",
     *      referencedColumnName="id"
     * )
     */
    protected $logo;

    /**
     * @ORM\OneToMany(
     *      targetEntity="\Erp\PropertyBundle\Entity\Property",
     *      mappedBy="user",
     *      cascade={"persist", "remove"},
     *      orphanRemoval=true
     * )
     * @ORM\OrderBy({"createdDate"="DESC"})
     * 
     * These are the properties of manager
     */
    protected $properties;

    /**
     * @ORM\OneToMany(
     *      targetEntity="\Erp\UserBundle\Entity\ForumTopic",
     *      mappedBy="user",
     *      cascade={"persist", "remove"},
     *      orphanRemoval=true
     * )
     */
    protected $forumTopics;

    /**
     * @ORM\OneToOne(
     *      targetEntity="\Erp\PropertyBundle\Entity\Property",
     *      mappedBy="tenantUser"
     * )
     */
    protected $tenantProperty;

    /**
     * @ORM\OneToMany(
     *      targetEntity="\Erp\UserBundle\Entity\UserDocument",
     *      mappedBy="fromUser",
     *      cascade={"persist"},
     *      orphanRemoval=true
     * )
     */
    protected $userDocuments;

    /**
     * @var string
     *
     * @ORM\Column(name="settings", type="text", nullable=true)
     */
    protected $settings;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_term_of_use", type="boolean")
     */
    protected $isTermOfUse = false;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="Erp\PaymentBundle\Entity\StripeCustomer",
     *      mappedBy="user",
     *      cascade={"persist", "remove"},
     *      orphanRemoval=true
     * )
     */
    protected $stripeCustomers;

    /**
     * @ORM\OneToOne(
     *      targetEntity="\Erp\PaymentBundle\Entity\StripeAccount",
     *      mappedBy="user",
     *      cascade={"persist", "remove"}
     * )
     */
    protected $stripeAccount;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="Erp\UserBundle\Entity\ProRequest",
     *      mappedBy="user",
     *      cascade={"persist"},
     *      orphanRemoval=true
     * )
     * @ORM\OrderBy({"updatedDate"="DESC"})
     */
    protected $proRequests;

    /**
     * @var ArrayCollection
     */
    protected $tenants;

    /**
     * @var int
     */
    protected $totalServiceRequests;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Erp\SmartMoveBundle\Entity\SmartMoveRenter", mappedBy="manager", cascade={"ALL"})
     * @ORM\OrderBy({"updatedDate"="DESC"})
     */
    protected $smartMoveRenters;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active_monthly_fee", type="boolean")
     */
    protected $isActiveMonthlyFee = false;

    /**
     * @var \Erp\UserBundle\Entity\Fee[]|Collection
     *
     * @ORM\OneToMany(targetEntity="Erp\UserBundle\Entity\Fee", mappedBy="user", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $fees;

    /**
     * @var RentPaymentBalance
     *
     * @ORM\OneToOne(targetEntity="Erp\UserBundle\Entity\RentPaymentBalance", mappedBy="user", cascade={"persist", "remove"})
     */
    protected $rentPaymentBalance;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_allow_rent_payment", type="boolean", nullable=true)
     */
    protected $allowRentPayment;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_allow_partial_payment", type="boolean", nullable=true)
     */
    private $allowPartialPayment;

    /**
     * @var User Manager
     * @ORM\ManyToOne(targetEntity="Erp\UserBundle\Entity\User", inversedBy="landlords")
     * @ORM\JoinColumn(
     *      name="manager_id",
     *      referencedColumnName="id",
     *      onDelete="SET NULL"
     * )
     */
    protected $manager;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Erp\UserBundle\Entity\User", mappedBy="manager")
     * @ORM\OrderBy({"firstName"="ASC","lastName"="ASC","createdDate"="DESC"})
     */
    protected $landlords;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Erp\UserBundle\Entity\Charge", mappedBy="manager", cascade={"ALL"})
     */
    protected $chargeOutgoings; //sent

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Erp\UserBundle\Entity\Charge", mappedBy="receiver", cascade={"ALL"})
     * @ORM\JoinColumn(
     *      name="receiver_id",
     *      referencedColumnName="id",
     *      onDelete="CASCADE"
     * )
     */
    protected $chargeIncomings; //received

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_agree_auto_withdrawal", type="boolean", nullable=true)
     */
    protected $agreeAutoWithdrawal;

    /**
     * @var Template
     *
     * @ORM\OneToMany(targetEntity="\Erp\NotificationBundle\Entity\Template", mappedBy="user")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $templates;

    /**
     * @var string
     *
     * @ORM\Column(name="primary_payment_type", length=2, type="string", nullable=true)
     */
    protected $primaryPaymentType;

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->properties = new ArrayCollection();
        $this->stripeCustomers = new ArrayCollection();
        $this->userDocuments = new ArrayCollection();
        $this->forumTopics = new ArrayCollection();
        $this->proRequests = new ArrayCollection();
        $this->tenants = new ArrayCollection();
        $this->landlords = new ArrayCollection();
        $this->chargeOutgoings = new ArrayCollection();
        $this->chargeIncomings = new ArrayCollection();
        $this->smartMoveRenters = new ArrayCollection();
        $this->fees = new ArrayCollection();
        $this->templates = new ArrayCollection();
    }

    /**
     * Constructor
     */
    public function __destruct() {
        unset($this->properties);
        unset($this->stripeCustomers);
        unset($this->userDocuments);
        unset($this->forumTopics);
        unset($this->proRequests);
        unset($this->tenants);
        unset($this->landlords);
        unset($this->chargeOutgoings);
        unset($this->chargeIncomings);
        unset($this->smartMoveRenters);
        unset($this->fees);
        unset($this->templates);
    }

    /**
     * @return string
     */
    public function __toString() {
        return (is_null($this->email) ? '' : $this->email);
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
     * @param $id
     *
     * @return $this
     */
    public function setId($id) {
        $this->id = (int) $id;

        return $this;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName) {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName() {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName) {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName() {
        return $this->lastName;
    }

    /**
     * Set companyName
     *
     * @param string $companyName
     *
     * @return $this
     */
    public function setCompanyName($companyName) {
        $this->companyName = $companyName;

        return $this;
    }

    /**
     * Get companyName
     *
     * @return string
     */
    public function getCompanyName() {
        return $this->companyName;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return $this
     */
    public function setPhone($phone) {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @param bool $isPlain
     *
     * @return string
     */
    public function getPhone($isPlain = false) {
        $phone = $isPlain ? str_replace(['(', ')', ' ', '-'], ['', '', '', ''], '1' . $this->phone) : $this->phone;

        return $phone;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhoneDigitsOnly() {
        $phone = str_replace(['(', ')', ' ', '-'], '', $this->phone);

        return $phone;
    }

    /**
     * Set websiteUrl
     *
     * @param string $websiteUrl
     *
     * @return $this
     */
    public function setWebsiteUrl($websiteUrl) {
        $this->websiteUrl = $websiteUrl;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getWebsiteUrl() {
        return $this->websiteUrl;
    }

    /**
     * Set addressOne
     *
     * @param string $addressOne
     *
     * @return $this
     */
    public function setAddressOne($addressOne) {
        $this->addressOne = $addressOne;

        return $this;
    }

    /**
     * Get addressOne
     *
     * @return string
     */
    public function getAddressOne() {
        return $this->addressOne;
    }

    /**
     * Set addressTwo
     *
     * @param string $addressTwo
     *
     * @return $this
     */
    public function setAddressTwo($addressTwo) {
        $this->addressTwo = $addressTwo;

        return $this;
    }

    /**
     * Get addressTwo
     *
     * @return string
     */
    public function getAddressTwo() {
        return $this->addressTwo;
    }
    
    /**
     * 
     */
    public function getFullAddress() {
        $address = $this->addressOne;
        if ($this->addressTwo) {
            $address .= ' ' . $this->addressTwo;
        }
        
        if ($address && $this->city) {
            $address .= ', ' . $this->city->getName();
            
            if ($this->postalCode) {
                $address .= ', ' . $this->postalCode;
                
                if ($this->state) {
                    $address .= ' ' . $this->state;
                }
            } else {
                if ($this->state) {
                    $address .= ', ' . $this->state;
                }
            }
            
            
        }
        
        return $address;
    }

    /**
     * Set state
     *
     * @param string $state
     *
     * @return $this
     */
    public function setState($state) {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getState() {
        return $this->state;
    }

    /**
     * Set postalCode
     *
     * @param string $postalCode
     *
     * @return $this
     */
    public function setPostalCode($postalCode) {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * Get postalCode
     *
     * @return string
     */
    public function getPostalCode() {
        return $this->postalCode;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return $this
     */
    public function setStatus($status) {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus() {
        return $this->status;
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

        return $this;
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
     */
    public function setUpdatedDate() {
        $this->updatedDate = new \DateTime();

        return $this;
    }

    /**
     * Get updatedDate
     *
     * @return \DateTime
     */
    public function getUpdatedDate() {
        return $this->updatedDate;
    }

    /**
     * Add property
     *
     * @param Property $property
     *
     * @return User
     */
    public function addProperty(Property $property) {
        $this->properties[] = $property;

        return $this;
    }

    /**
     * Remove property
     *
     * @param Property $property
     */
    public function removeProperty(Property $property) {
        $this->properties->removeElement($property);
    }

    /**
     * Get properties
     *
     * @return Collection
     */
    public function getProperties() {
        return $this->properties;
    }

    /**
     * Set image
     *
     * @param Image $image
     *
     * @return User
     */
    public function setImage(Image $image = null) {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return Image
     */
    public function getImage() {
        return $this->image;
    }

    /**
     * Set logo
     *
     * @param Image $logo
     *
     * @return User
     */
    public function setLogo(Image $logo = null) {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get image
     *
     * @return Image
     */
    public function getLogo() {
        return $this->logo;
    }

    /**
     * Set city
     *
     * @param City $city
     *
     * @return User
     */
    public function setCity(City $city = null) {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return City
     */
    public function getCity() {
        return $this->city;
    }

    /**
     * Set secondEmail
     *
     * @param string $secondEmail
     *
     * @return User
     */
    public function setSecondEmail($secondEmail = null) {
        if ($secondEmail) {
            $this->secondEmail = $secondEmail;
        }
        
        return $this;
    }

    /**
     * Get secondEmail
     *
     * @return string
     */
    public function getSecondEmail() {
        return $this->secondEmail;
    }

    /**
     * Set workPhone
     *
     * @param string $workPhone
     *
     * @return User
     */
    public function setWorkPhone($workPhone) {
        $this->workPhone = $workPhone;

        return $this;
    }

    /**
     * Get workPhone
     *
     * @return string
     */
    public function getWorkPhone() {
        return $this->workPhone;
    }

    /**
     * Set settings
     *
     * @param array $settings
     *
     * @return User
     */
    public function setSettings(array $settings) {
        $this->settings = (count($settings)) ? json_encode($settings, JSON_FORCE_OBJECT) : null;

        return $this;
    }

    /**
     * Get settings
     *
     * @return array
     */
    public function getSettings() {
        return json_decode($this->settings, true);
    }

    /**
     * Get setting
     *
     * @return bool
     */
    public function checkSetting($setting) {
        $settings = $this->getSettings();

        $return = in_array($setting, (array) $settings);

        return $return;
    }

    /**
     * Add stripeCustomer
     *
     * @param StripeCustomer $stripeCustomer
     *
     * @return $this
     */
    public function addStripeCustomer(StripeCustomer $stripeCustomer) {
        if ($this->stripeCustomers->count() == 0) {
            $this->primaryPaymentType = $stripeCustomer->getType();
        }
        
        $this->stripeCustomers[] = $stripeCustomer;

        return $this;
    }

    /**
     * Remove stripeCustomer
     *
     * @param StripeCustomer $stripeCustomer
     */
    public function removeStripeCustomer(StripeCustomer $stripeCustomer) {
        if ($this->stripeCustomers->contains($stripeCustomer)) {
            $this->stripeCustomers->removeElement($stripeCustomer);
        
            switch ($this->stripeCustomers->count()) {
                case 1:
                    $this->primaryPaymentType = $this->stripeCustomers->first()->getType();
                    break;
                case 0:
                    $this->primaryPaymentType = null;
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * Get stripeCustomers
     *
     * @return ArrayCollection
     */
    public function getStripeCustomers() {
        return $this->stripeCustomers;
    }

    /**
     * Set stripeCustomer
     *
     * @param \Erp\PaymentBundle\Entity\StripeCustomer $stripeCustomer
     *
     * @return User
     */
    public function setStripeCustomer(StripeCustomer $stripeCustomer) {
        $this->addStripeCustomer($stripeCustomer);

        return $this;
    }

    /**
     * Get the first stripeCustomer, or the one having type equal to $paymentType
     * if not null
     * 
     * @param string|null $paymentType
     * @return StripeCustomer
     * @throws \Exception
     */
    public function getStripeCustomer($paymentType = null) {
        if (is_null($paymentType)) {
            return $this->getStripeCustomers()->isEmpty() ? $this->getStripeCustomers() : $this->getStripeCustomers()->first();
        } elseif (($paymentType == StripeCustomer::BANK_ACCOUNT) || ($paymentType == StripeCustomer::CREDIT_CARD)) {
            $criteria = Criteria::create()->where(Criteria::expr()->eq('type', $paymentType));
            
            return $this->getStripeCustomers()->matching($criteria)->first();
        } else {
            throw new \Exception(sprintf('Invalid payment type provided to select the Stripe Customer of user %s (id %s, email %s)', $this->getFullName(), $this->id, $this->email));
        }
    }
    
    /**
     * 
     * @return boolean
     */
    public function hasStripeCustomers() {
        return !($this->hasNoStripeCustomers());
    }
    
    /**
     * 
     * @return boolean
     */
    public function hasNoStripeCustomers() {
        return $this->stripeCustomers->count() == 0;
    }

    /**
     * @return bool
     */
    public function isReadOnlyUser() {
        return ($this->getStatus() !== self::STATUS_ACTIVE) && $this->hasRole(self::ROLE_MANAGER);
    }

    /**
     * Set userDocuments
     *
     * @param UserDocument $userDocuments
     *
     * @return User
     */
    public function setUserDocuments(UserDocument $userDocuments) {
        $this->userDocuments = $userDocuments;

        return $this;
    }

    /**
     * Get userDocuments
     *
     * @return UserDocument
     */
    public function getUserDocuments() {
        return $this->userDocuments;
    }

    /**
     * Add userDocument
     *
     * @param UserDocument $userDocument
     *
     * @return User
     */
    public function addUserDocument(UserDocument $userDocument) {
        $this->userDocuments[] = $userDocument;

        return $this;
    }

    /**
     * Remove userDocument
     *
     * @param UserDocument $userDocument
     */
    public function removeUserDocument(UserDocument $userDocument) {
        $this->userDocuments->removeElement($userDocument);
    }

    /**
     * Add forumTopic
     *
     * @param ForumTopic $forumTopic
     *
     * @return User
     */
    public function addForumTopic(ForumTopic $forumTopic) {
        $this->forumTopics[] = $forumTopic;

        return $this;
    }

    /**
     * Remove forumTopic
     *
     * @param ForumTopic $forumTopic
     */
    public function removeForumTopic(ForumTopic $forumTopic) {
        $this->forumTopics->removeElement($forumTopic);
    }

    /**
     * Get forumTopics
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getForumTopics() {
        return $this->forumTopics;
    }

    /**
     * Add proRequest
     *
     * @param ProRequest $proRequest
     *
     * @return $this
     */
    public function addProRequest(ProRequest $proRequest) {
        $this->proRequests[] = $proRequest;

        return $this;
    }

    /**
     * Remove proRequest
     *
     * @param ProRequest $proRequest
     */
    public function removeProRequest(ProRequest $proRequest) {
        $this->proRequests->removeElement($proRequest);
    }

    /**
     * Get proRequests
     *
     * @return ArrayCollection
     */
    public function getProRequests() {
        return $this->proRequests;
    }

    /**
     * Get user full name with email
     *
     * @return string
     */
    public function getFullNameWithEmail() {
        return $this->firstName . ' ' . $this->lastName . ' (' . $this->email . ')';
    }

    /**
     * Set tenantProperty
     *
     * @param Property $tenantProperty
     *
     * @return User
     */
    public function setTenantProperty(Property $tenantProperty = null) {
        $this->tenantProperty = $tenantProperty;

        return $this;
    }

    /**
     * Get tenantProperty
     *
     * @return Property
     */
    public function getTenantProperty() {
        return $this->tenantProperty;
    }

    /**
     * Get tenants
     *
     * @return ArrayCollection
     */
    public function getTenants() {
        $properties = $this->getProperties();

        if ($properties) {
            /** @var Property $property */
            foreach ($properties as $property) {
                if ($property->getTenantUser()) {
                    $this->tenants[] = $property->getTenantUser();
                }
            }
        }

        return $this->tenants;
    }
    
    /**
     * Check is tenant
     *
     * @param User $tenant
     *
     * @return bool
     */
    public function isTenant(User $tenant) {
        if ($this->hasRole(User::ROLE_TENANT)) {
            return true;
        } else {
            $properties = $this->getProperties();

            if ($properties) {
                /** @var Property $property */
                foreach ($properties as $property) {
                    if ($property->getTenantUser() && $property->getTenantUser() == $tenant) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * @return int
     */
    public function getTotalServiceRequests() {
        return $this->totalServiceRequests;
    }

    /**
     * @param $count
     */
    public function setTotalServiceRequests($count) {
        $this->totalServiceRequests = $count;
    }

    /**
     * Add smartMoveRenter
     *
     * @param SmartMoveRenter $smartMoveRenter
     *
     * @return $this
     */
    public function addSmartMoveRenter(SmartMoveRenter $smartMoveRenter) {
        $this->smartMoveRenters[] = $smartMoveRenter;

        return $this;
    }

    /**
     * Remove smartMoveRenter
     *
     * @param SmartMoveRenter $smartMoveRenter
     */
    public function removeSmartMoveRenter(SmartMoveRenter $smartMoveRenter) {
        $this->smartMoveRenters->removeElement($smartMoveRenter);
    }

    /**
     * Get smartMoveRenters
     *
     * @return ArrayCollection
     */
    public function getSmartMoveRenters() {
        return $this->smartMoveRenters;
    }

    /**
     * Set propertyCounter
     *
     * @param integer $propertyCounter
     *
     * @return User
     */
    public function setPropertyCounter($propertyCounter) {
        $this->propertyCounter = $propertyCounter;

        return $this;
    }

    /**
     * Get propertyCounter
     *
     * @return integer
     */
    public function getPropertyCounter() {
        return $this->propertyCounter;
    }

    /**
     * Set applicationFormCounter
     *
     * @param integer $applicationFormCounter
     *
     * @return User
     */
    public function setApplicationFormCounter($applicationFormCounter) {
        $this->applicationFormCounter = $applicationFormCounter;

        return $this;
    }

    /**
     * Get applicationFormCounter
     *
     * @return integer
     */
    public function getApplicationFormCounter() {
        return $this->applicationFormCounter;
    }

    /**
     * Set isTermOfUse
     *
     * @param boolean $isTermOfUse
     *
     * @return User
     */
    public function setIsTermOfUse($isTermOfUse) {
        $this->isTermOfUse = $isTermOfUse;

        return $this;
    }

    /**
     * Get isTermOfUse
     *
     * @return boolean
     */
    public function getIsTermOfUse() {
        return $this->isTermOfUse;
    }

    /**
     * Set contractFormCounter
     *
     * @param integer $contractFormCounter
     *
     * @return User
     */
    public function setContractFormCounter($contractFormCounter) {
        $this->contractFormCounter = $contractFormCounter;

        return $this;
    }

    /**
     * Get contractFormCounter
     *
     * @return integer
     */
    public function getContractFormCounter() {
        return $this->contractFormCounter;
    }

    /**
     * Set isPropertyCounterFree
     *
     * @param boolean $isPropertyCounterFree
     *
     * @return User
     */
    public function setIsPropertyCounterFree($isPropertyCounterFree) {
        $this->isPropertyCounterFree = $isPropertyCounterFree;

        return $this;
    }

    /**
     * Get isPropertyCounterFree
     *
     * @return boolean
     */
    public function getIsPropertyCounterFree() {
        return $this->isPropertyCounterFree;
    }

    /**
     * Set isApplicationFormCounterFree
     *
     * @param boolean $isApplicationFormCounterFree
     *
     * @return User
     */
    public function setIsApplicationFormCounterFree($isApplicationFormCounterFree) {
        $this->isApplicationFormCounterFree = $isApplicationFormCounterFree;

        return $this;
    }

    /**
     * Get isApplicationFormCounterFree
     *
     * @return boolean
     */
    public function getIsApplicationFormCounterFree() {
        return $this->isApplicationFormCounterFree;
    }

    /**
     * Set isActiveMonthlyFee
     *
     * @param boolean $isActiveMonthlyFee
     *
     * @return User
     */
    public function setIsActiveMonthlyFee($isActiveMonthlyFee = false) {
        $this->isActiveMonthlyFee = $isActiveMonthlyFee;

        return $this;
    }

    /**
     * Get isActiveMonthlyFee
     *
     * @return boolean
     */
    public function getIsActiveMonthlyFee() {
        return $this->isActiveMonthlyFee;
    }

    /**
     * Set stripeAccount
     *
     * @param \Erp\PaymentBundle\Entity\StripeAccount $stripeAccount
     *
     * @return User
     */
    public function setStripeAccount(\Erp\PaymentBundle\Entity\StripeAccount $stripeAccount = null) {
        $this->stripeAccount = $stripeAccount;

        return $this;
    }

    /**
     * Get stripeAccount
     *
     * @return \Erp\PaymentBundle\Entity\StripeAccount
     */
    public function getStripeAccount() {
        return $this->stripeAccount;
    }

    /**
     * @return bool
     */
    public function isActive() {
        return self::STATUS_ACTIVE === $this->status;
    }

    /**
     * @return bool
     */
    public function hasAccessToPaymentPage() {
        return $this->hasRole(self::ROLE_MANAGER) && !$this->hasRole(self::ROLE_TENANT);
    }

    /**
     * 
     * @return bool
     */
    public function hasStripeAccount() {
        return null !== $this->stripeAccount;
    }

    /**
     * @return ArrayCollection|Collection
     */
    public function getActiveProperties() {
        $criteria = Criteria::create()
                ->where(Criteria::expr()->neq('status', self::STATUS_DELETED));

        return $this->properties->matching($criteria);
    }
    
    /**
     * 
     * @return int
     */
    public function getNumberOfActiveProperties() {
        return $this->getActiveProperties()->count();
    }
    
    /**
     * 
     * @param \Erp\UserBundle\Entity\User $user
     * @return boolean
     */
    public function hasTenant(User $user) {
        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->eq('tenantUser', $user));

        return $this->properties->matching($criteria)->isEmpty();
    }

    /**
     * 
     * @return ArrayCollection|Collection
     */
    public function getPropertiesWithTenants() {
        $criteria = Criteria::create()
                ->where(Criteria::expr()->neq('tenantUser', null));

        return $this->properties->matching($criteria);
    }

    /**
     * Add landlord
     *
     * @param User $landlord
     *
     * @return User
     */
    public function addLandlord(User $landlord) {
        $this->landlords[] = $landlord;

        return $this;
    }

    /**
     * Remove landlord
     *
     * @param User $landlord
     */
    public function removeLandlord(User $landlord) {
        $this->landlords->removeElement($landlord);
    }

    /**
     * Get landlords
     *
     * @return Collection
     */
    public function getLandlords() {
        return $this->landlords;
    }

    /**
     * Get manager
     *
     * @return User
     */
    public function getManager() {
        return $this->manager;
    }

    /**
     * 
     * @return User
     */
    public function getRealManager() {
        $result = $this->getManager();
        $theRoles = $this->getRoles();
        
        if ($result) {
            if (empty($theRoles) || in_array(self::ROLE_USER, $theRoles)) {
                return $result;
            } else {
                if (in_array(self::ROLE_TENANT, $theRoles)) {
                    if (in_array(self::ROLE_MANAGER, $result->getRoles())) {
                        return $result;
                    } else {
                        return $result->getManager();
    }
                }
            }
        } else {
            return $this;
        }
    }

    /**
     * Set manager
     *
     * @param User $manager
     *
     * @return User
     */
    public function setManager(User $manager) {
        $this->manager = $manager;
        
        if ($this->hasRole(User::ROLE_LANDLORD)) {
            $manager->addLandlord($this);
        }

        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getFullName() {
        return sprintf('%s %s', $this->firstName, $this->lastName);
    }

    /**
     * Add fee
     *
     * @param \Erp\UserBundle\Entity\Fee $fee
     *
     * @return User
     */
    public function addFee(\Erp\UserBundle\Entity\Fee $fee) {
        $fee->setUser($this);
        $this->fees[] = $fee;

        return $this;
    }

    /**
     * Remove fee
     *
     * @param \Erp\UserBundle\Entity\Fee $fee
     */
    public function removeFee(\Erp\UserBundle\Entity\Fee $fee) {
        $this->fees->removeElement($fee);
    }

    /**
     * Get fees
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFees() {
        return $this->fees;
    }

    /**
     * Set rentPaymentBalance
     *
     * @param \Erp\UserBundle\Entity\RentPaymentBalance $rentPaymentBalance
     *
     * @return User
     */
    public function setRentPaymentBalance(\Erp\UserBundle\Entity\RentPaymentBalance $rentPaymentBalance = null) {
        $this->rentPaymentBalance = $rentPaymentBalance;

        return $this;
    }

    /**
     * Get rentPaymentBalance
     *
     * @return \Erp\UserBundle\Entity\RentPaymentBalance
     */
    public function getRentPaymentBalance() {
        return $this->rentPaymentBalance;
    }

    /**
     * Set allowRentPayment
     *
     * @param boolean $allowRentPayment
     *
     * @return User
     */
    public function setAllowRentPayment($allowRentPayment) {
        $this->allowRentPayment = (bool) $allowRentPayment;

        return $this;
    }

    /**
     * Get allowRentPayment
     *
     * @return boolean
     */
    public function getAllowRentPayment() {
        return $this->allowRentPayment;
    }

    /**
     * Set allowPartialPayment
     *
     * @param boolean $allowPartialPayment
     *
     * @return User
     */
    public function setAllowPartialPayment($allowPartialPayment) {
        $this->allowPartialPayment = $allowPartialPayment;

        return $this;
    }

    /**
     * Get allowPartialPayment
     *
     * @return boolean
     */
    public function getAllowPartialPayment() {
        return $this->allowPartialPayment;
    }

    public function isAllowRentPayment() {
        return $this->allowRentPayment;
    }

    /**
     * Add chargeOutgoing
     *
     * @param Charge $chargeOutgoing
     *
     * @return Charge
     */
    public function addChargeOutgoing(Charge $chargeOutgoing) {
        $this->chargeOutgoings[] = $chargeOutgoing;

        return $chargeOutgoing;
    }

    /**
     * Remove chargeOutgoing
     *
     * @param Charge $chargeOutgoing
     */
    public function removeChargeOutgoing(Charge $chargeOutgoing) {
        $this->chargeOutgoings->removeElement($chargeOutgoing);
    }

    /**
     * Get chargeOutgoings
     *
     * @return Collection
     */
    public function getChargeOutgoings() {
        return $this->chargeOutgoings;
    }

    /**
     * Add chargeIncoming
     *
     * @param Charge $chargeIncoming
     *
     * @return Charge
     */
    public function addChargeIncoming(Charge $chargeIncoming) {
        $this->chargeIncomings[] = $chargeIncoming;

        return $this;
    }

    /**
     * Remove chargeIncoming
     *
     * @param Charge $chargeIncoming
     */
    public function removeChargeIncoming(Charge $chargeIncoming) {
        $this->chargeIncomings->removeElement($chargeIncoming);
    }

    /**
     * Get chargeIncomings
     *
     * @return Collection
     */
    public function getChargeIncomings() {
        return $this->chargeIncomings;
    }

    /**
     * 
     * @return type
     */
    public function getTotalOwedAmount() {
        $rentPaymentBalance = $this->rentPaymentBalance->getBalance();

        return $rentPaymentBalance >= 0 ? 0 : abs($rentPaymentBalance);
    }

    /**
     * 
     */
    public function clearFees() {
        $this->fees->clear();
    }

    /**
     * 
     */
    public function clearProperties() {
        $this->properties->clear();
    }

    /**
     * Set agreeAutoWithdrawal
     *
     * @param boolean $agreeAutoWithdrawal
     *
     * @return User
     */
    public function setAgreeAutoWithdrawal($agreeAutoWithdrawal) {
        $this->agreeAutoWithdrawal = $agreeAutoWithdrawal;

        return $this;
    }

    /**
     * Get agreeAutoWithdrawal
     *
     * @return boolean
     */
    public function getAgreeAutoWithdrawal() {
        return $this->agreeAutoWithdrawal;
    }

    /**
     * 
     * @return type
     */
    public function isAgreeAutoWithdrawal() {
        return $this->agreeAutoWithdrawal;
    }

    /**
     * 
     * @return boolean
     */
    public function isDebtor() {
        // TODO Create Payment balance when tenant register
        if (!$this->rentPaymentBalance) {
            return false;
        }

        $balance = $this->rentPaymentBalance->getBalance();

        return $balance < 0;
    }

    /**
     * Add template
     *
     * @param Template $template
     *
     * @return Template
     */
    public function addTemplate(Template $template) {
        $this->templates[] = $template;

        return $template;
    }

    /**
     * Remove template
     *
     * @param Template $template
     */
    public function removeTemplate(Template $template) {
        $this->templates->removeElement($template);
    }

    /**
     * Get templates
     *
     * @return Collection
     */
    public function getTemplates() {
        return $this->templates;
    }

    public function getSubjectForEmail() {
        $title = $this->getCompanyName();
        if (!$title) {
            $title = $this->getFullName();
        }
        if (!$title) {
            $title = 'Zoobdoo';
        }
        return $title;
    }

    public function getFromForEmail() {
        $title = $this->getFullName();
        if (!$title) {
            $title = 'Zoobdoo';
        }
        return $title;
    }

    /**
     * Set primaryPaymentType
     *
     * @param string $primaryPaymentType
     * @return $this
     */
    public function setPrimaryPaymentType($primaryPaymentType) {
        if ($primaryPaymentType == self::PRIMARY_PAYMENT_TYPE_BANK_ACCOUNT || $primaryPaymentType == self::PRIMARY_PAYMENT_TYPE_CREDIT_CARD) {
            $this->primaryPaymentType = $primaryPaymentType;
        }

        return $this;
    }

    /**
     * Get primaryPaymentType
     *
     * @return string
     */
    public function getPrimaryPaymentType() {
        return $this->primaryPaymentType;
    }

}
