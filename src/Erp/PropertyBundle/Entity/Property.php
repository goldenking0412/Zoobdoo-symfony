<?php

namespace Erp\PropertyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Erp\NotificationBundle\Entity\UserNotification;
use Erp\SmartMoveBundle\Entity\SmartMoveRenter;
use Erp\StripeBundle\Entity\Transaction;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Erp\UserBundle\Entity\User;
use Erp\CoreBundle\Entity\City;
use Erp\CoreBundle\Entity\Image;
use Erp\CoreBundle\Entity\Document;
use Erp\PropertyBundle\Entity\AppointmentRequest;
use Erp\UserBundle\Entity\InvitedUser;
use Erp\PropertyBundle\Entity\ApplicationForm;
use Erp\PropertyBundle\Entity\PropertyRepostRequest;
use Erp\PropertyBundle\Entity\PropertySettings;
use Erp\PropertyBundle\Entity\PropertySecurityDeposit;
use Erp\PaymentBundle\Entity\StripeDepositAccount;

/**
 * Property
 *
 * @ORM\Table(name="properties")
 * @ORM\Entity(repositoryClass="Erp\PropertyBundle\Repository\PropertyRepository")
 * @UniqueEntity(
 *     fields={"name", "stateCode", "city", "status"},
 *     message="Name is already in use in your city",
 *
 *     groups={"EditProperty"}
 * )
 * @ORM\HasLifecycleCallbacks()
 */
class Property {

    const STATUS_AVAILABLE = 'available';
    const STATUS_RENTED = 'rented';
    const STATUS_DRAFT = 'draft';
    const STATUS_DELETED = 'deleted';
    const LIMIT_AVAILABLE_PER_PAGE = 8;
    const LIMIT_SEARCH_PER_PAGE = 9;
    const LIMIT_USER_LISTINGS = 9;
    const FILED_STATUS = 'status';
    const TYPE_APARTMENT = 'Apartment';
    const TYPE_SINGLE_FAMILY_HOME = 'Single Family Home';
    const TYPE_DUPLEX_TRIPLEX_TOWNHOME = 'Duplex/Triplex/Townhome';
    const TYPE_MOBILE_HOME = 'Mobile Home/Manufactured Home';
    const TYPE_STUDENT_HOUSING = 'Student Housing';
    const TYPE_COMMERCIAL = 'Commercial';
    const REPOSITORY = 'ErpPropertyBundle:Property';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var User this is the manager
     * 
     * @ORM\ManyToOne(
     *      targetEntity="\Erp\UserBundle\Entity\User",
     *      inversedBy="properties"
     * )
     * @ORM\JoinColumn(
     *      name="user_id",
     *      referencedColumnName="id",
     *      onDelete="CASCADE"
     * )
     */
    protected $user;

    /**
     * @var User
     * 
     * @ORM\OneToOne(
     *      targetEntity="\Erp\UserBundle\Entity\User",
     *      inversedBy="tenantProperty"
     * )
     * @ORM\JoinColumn(
     *      name="tenant_user_id",
     *      referencedColumnName="id"
     * )
     */
    protected $tenantUser;

    /**
     * @var User
     * 
     * @ORM\ManyToOne(
     *      targetEntity="\Erp\UserBundle\Entity\User",
     *      inversedBy="properties"
     * )
     * @ORM\JoinColumn(
     *      name="landlord_user_id",
     *      referencedColumnName="id"
     * )
     */
    protected $landlordUser;

    /**
     * @ORM\OneToMany(
     *      targetEntity="\Erp\StripeBundle\Entity\Transaction",
     *      mappedBy="property",
     *      cascade={"persist","remove"},
     *      orphanRemoval=true
     * )
     * @ORM\JoinColumn(
     *      name="property_id",
     *      referencedColumnName="id",
     *      onDelete="CASCADE",
     *      nullable=true
     * )
     */
    protected $transactions;

    /**
     * @ORM\ManyToOne(
     *      targetEntity="\Erp\CoreBundle\Entity\City",
     *      inversedBy="properties"
     * )
     * @ORM\JoinColumn(
     *      name="city_id",
     *      referencedColumnName="id",
     *      onDelete="CASCADE",
     *      nullable=true,
     * )
     *
     * @Assert\NotBlank(
     *     message="Please select City",
     *     groups={"EditProperty"}
     * )
     */
    protected $city;

    /**
     * @ORM\ManyToMany(
     *      targetEntity="\Erp\CoreBundle\Entity\Image",
     *      cascade={"persist","remove"},
     *      orphanRemoval=true
     * )
     * @ORM\JoinTable(name="property_images",
     *      joinColumns={
     *          @ORM\JoinColumn(name="property_id", referencedColumnName="id", onDelete="CASCADE")
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="image_id", referencedColumnName="id", onDelete="CASCADE")
     *      }
     * )
     */
    protected $images;

    /**
     * @ORM\ManyToMany(
     *      targetEntity="\Erp\PropertyBundle\Entity\LeaseOutHistory",
     *      cascade={"persist"},
     *      orphanRemoval=true
     * )
     * @ORM\JoinTable(name="property_lease_out_history",
     *      joinColumns={
     *          @ORM\JoinColumn(name="property_id", referencedColumnName="id")
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="lease_out_history", referencedColumnName="id")
     *      }
     * )
     */
    protected $leaseOutHistory;

    /**
     * @ORM\ManyToMany(
     *      targetEntity="\Erp\CoreBundle\Entity\Document",
     *      cascade={"persist"},
     *      orphanRemoval=true
     * )
     * @ORM\JoinTable(name="property_documents",
     *      joinColumns={
     *          @ORM\JoinColumn(name="property_id", referencedColumnName="id")
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="document_id", referencedColumnName="id")
     *      }
     * )
     */
    protected $documents;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     *
     * @Assert\NotBlank(
     *     message="Please enter property Name",
     *     groups={"EditProperty"}
     * )
     *
     * @Assert\Regex(
     *     pattern="/^([\w\s-])+$/",
     *     message="Name should only have: letters, numbers, spaces and symbols '-, _'",
     *     groups={"EditProperty"}
     * )
     *
     * @Assert\Length(
     *     min=2,
     *     max="255",
     *     minMessage="Name should have minimum 2 characters and maximum 255 characters",
     *     maxMessage="Name should have minimum 2 characters and maximum 255 characters",
     *     groups={"EditProperty"}
     * )
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     *
     * @Assert\NotBlank(
     *     message="Please enter Address",
     *     groups={"EditProperty"}
     * )
     *
     * @Assert\Length(
     *     min=2,
     *     max="255",
     *     minMessage="Address should have minimum 2 characters and maximum 255 characters",
     *     maxMessage="Address should have minimum 2 characters and maximum 255 characters",
     *     groups={"EditProperty"}
     * )
     */
    protected $address;

    /**
     * @var integer
     *
     * @ORM\Column(name="city_id", type="integer", nullable=true)
     */
    protected $cityId;

    /**
     * @var string
     *
     * @ORM\Column(name="state_code", type="string", length=4, nullable=true)
     *
     * @Assert\NotBlank(
     *     message="Please select State",
     *     groups={"EditProperty"}
     * )
     */
    protected $stateCode;

    /**
     * @var string
     *
     * @ORM\Column(name="zip", type="string", length=6, nullable=true)
     *
     * @Assert\NotBlank(
     *     message="Please enter Zip Code",
     *     groups={"EditProperty"}
     * )
     *
     * @Assert\Regex(
     *     pattern="/^[0-9]+$/",
     *     match=true,
     *     message="Zip code must contain numbers",
     *     groups={"EditProperty"}
     * )
     * @Assert\Length(
     *     min=5,
     *     max=5,
     *     minMessage="Zip code should have minimum 5 characters and maximum 5 characters",
     *     maxMessage="Zip code should have minimum 5 characters and maximum 5 characters",
     *     groups={"EditProperty"}
     * )
     */
    protected $zip;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float", nullable=true)
     */
    protected $price;

    /**
     * @var string
     *
     * @ORM\Column(name="of_beds", type="string", length=255,  nullable=true)
     */
    protected $ofBeds;

    /**
     * @var string
     *
     * @ORM\Column(name="of_baths", type="string", length=255, nullable=true)
     */
    protected $ofBaths;

    /**
     * @var float
     *
     * @ORM\Column(name="square_footage", type="float", nullable=true)
     *
     * @Assert\NotBlank(
     *      message="Please enter Square Footage",
     *      groups={"EditProperty"}
     * )
     */
    protected $squareFootage;

    /**
     * @var string
     *
     * @ORM\Column(name="amenities", type="text", nullable=true)
     */
    protected $amenities;

    /**
     * @var string
     *
     * @ORM\Column(name="about_properties", type="text", nullable=true)
     */
    protected $aboutProperties;

    /**
     * @var string
     *
     * @ORM\Column(name="additional_details", type="text", nullable=true)
     */
    protected $additionalDetails;

    /**
     * @var string
     *
     * @ORM\Column(
     *      name="status",
     *      type="string",
     *      length=16,
     *      nullable=true
     * )
     */
    protected $status = self::STATUS_DRAFT;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", nullable=true)
     */
    protected $type = self::TYPE_APARTMENT;

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
     * @var \DateTime
     *
     * @ORM\Column(name="paid_date", type="datetime", nullable=true)
     */
    protected $paidDate;

    /**
     * @var AppointmentRequest
     *
     * @ORM\OneToMany(
     *      targetEntity="Erp\PropertyBundle\Entity\AppointmentRequest",
     *      mappedBy="property",
     *      cascade={"persist"},
     *      orphanRemoval=true
     * )
     * @ORM\OrderBy({"updatedDate"="DESC"})
     */
    protected $appointmentRequests;

    /**
     * @var InvitedUser
     *
     * @ORM\OneToMany(
     *      targetEntity="Erp\UserBundle\Entity\InvitedUser",
     *      mappedBy="property",
     *      cascade={"persist","remove"},
     *      orphanRemoval=true
     * )
     * @ORM\OrderBy({"createdDate"="ASC"})
     */
    protected $invitedUsers;

    /**
     * @ORM\OneToOne(
     *      targetEntity="\Erp\PropertyBundle\Entity\ContractForm",
     *      mappedBy="property",
     *      cascade={"persist"},
     *      orphanRemoval=true
     * )
     */
    protected $contractForm;

    /**
     * @ORM\OneToOne(
     *      targetEntity="\Erp\PropertyBundle\Entity\ApplicationForm",
     *      mappedBy="property",
     *      cascade={"persist", "remove"},
     *      orphanRemoval=true
     * )
     */
    protected $applicationForm;

    /**
     * @ORM\OneToMany(
     *      targetEntity="\Erp\PropertyBundle\Entity\PropertyRepostRequest",
     *      mappedBy="property",
     *      cascade={"persist","remove"},
     *      orphanRemoval=true
     * )
     * @ORM\JoinColumn(
     *      name="property_repost_request_id",
     *      referencedColumnName="id",
     *      onDelete="CASCADE"
     * )
     */
    protected $propertyRepostRequests;

    /**
     * @var PropertySettings
     *
     * @ORM\OneToOne(targetEntity="\Erp\PropertyBundle\Entity\PropertySettings", mappedBy="property", cascade={"persist"})
     * @ORM\JoinColumn(name="settings_id", referencedColumnName="id")
     */
    protected $settings;

    /**
     * @var PropertySecurityDeposit
     *
     * @ORM\OneToOne(targetEntity="\Erp\PropertyBundle\Entity\PropertySecurityDeposit", cascade={"persist"})
     * @ORM\JoinColumn(name="deposit_id", referencedColumnName="id")
     */
    protected $securityDeposit;

    /**
     * @var StripeDepositAccount
     *
     * @ORM\OneToOne(targetEntity="Erp\PaymentBundle\Entity\StripeDepositAccount", cascade={"persist"})
     * @ORM\JoinColumn(name="deposit_account_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    protected $depositAccount;

    /**
     * @ORM\OneToMany(
     *      targetEntity="\Erp\PropertyBundle\Entity\PropertyRentHistory",
     *      mappedBy="property",
     *      cascade={"persist","remove"},
     *      orphanRemoval=true
     * )
     * @ORM\JoinColumn(
     *      name="property_rent_history_id",
     *      referencedColumnName="id",
     *      onDelete="CASCADE"
     * )
     */
    protected $history;

    /**
     * @ORM\OneToMany(
     *      targetEntity="\Erp\PropertyBundle\Entity\ScheduledRentPayment",
     *      mappedBy="property",
     *      cascade={"persist","remove"},
     *      orphanRemoval=true
     * )
     * @ORM\JoinColumn(
     *      name="property_id",
     *      referencedColumnName="id",
     *      onDelete="CASCADE"
     * )
     */
    protected $scheduledRentPayments;

    /**
     * Constructor
     */
    public function __construct() {
        $this->images = new ArrayCollection();
        $this->leaseOutHistory = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->appointmentRequests = new ArrayCollection();
        $this->invitedUsers = new ArrayCollection();
        $this->propertyRepostRequests = new ArrayCollection();
        $this->history = new ArrayCollection();
        $this->transactions = new ArrayCollection();
        $this->scheduledRentPayments = new ArrayCollection();
    }
    
    public function __destruct() {
        unset($this->images);
        unset($this->leaseOutHistory);
        unset($this->documents);
        unset($this->appointmentRequests);
        unset($this->invitedUsers);
        unset($this->propertyRepostRequests);
        unset($this->history);
        unset($this->transactions);
        unset($this->scheduledRentPayments);
    }

    public function __clone() {
        $this->status = self::STATUS_DRAFT;
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->name ? $this->name : '';
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
     * Set name
     *
     * @param string $name
     *
     * @return Property
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
     * Set address
     *
     * @param string $address
     *
     * @return Property
     */
    public function setAddress($address) {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress() {
        return $this->address;
    }

    /**
     * Set cityId
     *
     * @param integer $cityId
     *
     * @return Property
     */
    public function setCityId($cityId) {
        $this->cityId = $cityId;

        return $this;
    }

    /**
     * Get cityId
     *
     * @return integer
     */
    public function getCityId() {
        return $this->cityId;
    }

    /**
     * Set zip
     *
     * @param string $zip
     *
     * @return Property
     */
    public function setZip($zip) {
        $this->zip = $zip;

        return $this;
    }

    /**
     * Get zip
     *
     * @return string
     */
    public function getZip() {
        return $this->zip;
    }

    /**
     * Set ofBeds
     *
     * @param string $ofBeds
     *
     * @return Property
     */
    public function setOfBeds($ofBeds) {
        $this->ofBeds = $ofBeds;

        return $this;
    }

    /**
     * Get ofBeds
     *
     * @return string
     */
    public function getOfBeds() {
        return $this->ofBeds;
    }

    /**
     * Set ofBaths
     *
     * @param string $ofBaths
     *
     * @return Property
     */
    public function setOfBaths($ofBaths) {
        $this->ofBaths = $ofBaths;

        return $this;
    }

    /**
     * Get ofBaths
     *
     * @return string
     */
    public function getOfBaths() {
        return $this->ofBaths;
    }

    /**
     * Set squareFootage
     *
     * @param string $squareFootage
     *
     * @return Property
     */
    public function setSquareFootage($squareFootage) {
        $this->squareFootage = $squareFootage;

        return $this;
    }

    /**
     * Get squareFootage
     *
     * @return string
     */
    public function getSquareFootage() {
        return $this->squareFootage;
    }

    /**
     * Set amenities
     *
     * @param string $amenities
     *
     * @return Property
     */
    public function setAmenities($amenities) {
        $this->amenities = $amenities;

        return $this;
    }

    /**
     * Get amenities
     *
     * @return string
     */
    public function getAmenities() {
        return $this->amenities;
    }

    /**
     * Set aboutProperties
     *
     * @param string $aboutProperties
     *
     * @return Property
     */
    public function setAboutProperties($aboutProperties) {
        $this->aboutProperties = $aboutProperties;

        return $this;
    }

    /**
     * Get aboutProperties
     *
     * @return string
     */
    public function getAboutProperties() {
        return $this->aboutProperties;
    }

    /**
     * Set additionalDetails
     *
     * @param string $additionalDetails
     *
     * @return Property
     */
    public function setAdditionalDetails($additionalDetails) {
        $this->additionalDetails = $additionalDetails;

        return $this;
    }

    /**
     * Get additionalDetails
     *
     * @return string
     */
    public function getAdditionalDetails() {
        return $this->additionalDetails;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Property
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
     * Set property type
     *
     * @param string $type
     *
     * @return Property
     */
    public function setType($type) {
        $this->type = $type;

        return $this;
    }

    /**
     * Get property type
     *
     * @return string
     */
    public function getType() {
        return $this->type;
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
     * Set updatedDate
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
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

    /**
     * Set user
     *
     * @param User $user
     *
     * @return Property
     */
    public function setUser(User $user = null) {
        $this->user = $user;
        
        $user->addProperty($this);

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
     * Set city
     *
     * @param City $city
     *
     * @return Property
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

    public function getCityName() {
        $out = '';
        if ($this->city) {
            $out = $this->city->getName();
        }
        return $out;
    }

    /**
     * Set stateCode
     *
     * @param string $stateCode
     *
     * @return Property
     */
    public function setStateCode($stateCode) {
        $this->stateCode = $stateCode;

        return $this;
    }

    /**
     * Get stateCode
     *
     * @return string
     */
    public function getStateCode() {
        return $this->stateCode;
    }

    /**
     * Add image
     *
     * @param Image $image
     *
     * @return Property
     */
    public function addImage(Image $image) {
        $this->images[] = $image;

        return $this;
    }

    /**
     * Remove image
     *
     * @param Image $image
     */
    public function removeImage(Image $image) {
        $this->images->removeElement($image);
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImages() {
        return $this->images;
    }

    /**
     * Add lease out history
     *
     * @param LeaseOutHistory $leaseOutHistory
     *
     * @return Property
     */
    public function addLeaseOutHistory(LeaseOutHistory $leaseOutHistory) {
        $this->leaseOutHistory[] = $leaseOutHistory;

        return $this;
    }

    /**
     * Remove LeaseOutHistory
     *
     * @param LeaseOutHistory $leaseOutHistory
     */
    public function removeLeaseOutHistory(LeaseOutHistory $leaseOutHistory) {
        $this->leaseOutHistory->removeElement($leaseOutHistory);
    }

    /**
     * Get leaseOutHistory
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLeaseOutHistory() {
        return $this->leaseOutHistory;
    }

    /**
     * Add document
     *
     * @param Document $document
     *
     * @return Property
     */
    public function addDocument(Document $document) {
        $this->documents[] = $document;

        return $this;
    }

    /**
     * Remove document
     *
     * @param Document $document
     */
    public function removeDocument(Document $document) {
        $this->documents->removeElement($document);
    }

    /**
     * Get documents
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDocuments() {
        return $this->documents;
    }

    /**
     * Get Property full address
     *
     * @return string
     */
    public function getFullAddress() {
        $city = $this->getCity() ? $this->getCity()->getName() . ', ' : ' ';
        $address = $this->getAddress() ? $this->getAddress() . ', ' : ' ';
        $fullAddress = $address . $city . $this->getStateCode() . ' ' . $this->getZip();

        return $fullAddress;
    }

    /**
     * Set tenantUser
     *
     * @param \Erp\UserBundle\Entity\User $tenantUser
     *
     * @return Property
     */
    public function setTenantUser(User $tenantUser = null) {
        $this->tenantUser = $tenantUser;
        
        $tenantUser->setTenantProperty($this);

        return $this;
    }

    /**
     * Get tenantUser
     *
     * @return \Erp\UserBundle\Entity\User
     */
    public function getTenantUser() {
        return $this->tenantUser;
    }

    /**
     * Set landlordUser
     *
     * @param \Erp\UserBundle\Entity\User $landlordUser
     *
     * @return Property
     */
    public function setLandlordUser(User $landlordUser = null) {
        $this->landlordUser = $landlordUser;

        return $this;
    }

    /**
     * Get landlordUser
     *
     * @return \Erp\UserBundle\Entity\User
     */
    public function getLandlordUser() {
        return $this->landlordUser;
    }

    /**
     * Add appointmentRequest
     *
     * @param AppointmentRequest $appointmentRequest
     *
     * @return $this
     */
    public function addAppointmentRequest(AppointmentRequest $appointmentRequest) {
        $this->appointmentRequests[] = $appointmentRequest;

        return $this;
    }

    /**
     * Remove appointmentRequest
     *
     * @param AppointmentRequest $appointmentRequest
     */
    public function removeAppointmentRequest(AppointmentRequest $appointmentRequest) {
        $this->appointmentRequests->removeElement($appointmentRequest);
    }

    /**
     * Get appointmentRequests
     *
     * @return ArrayCollection|AppointmentRequest
     */
    public function getAppointmentRequests() {
        return $this->appointmentRequests;
    }

    /**
     * Add invitedUser
     *
     * @param InvitedUser $invitedUser
     *
     * @return Property
     */
    public function addInvitedUser(InvitedUser $invitedUser) {
        $this->invitedUsers[] = $invitedUser;

        return $this;
    }

    /**
     * Remove invitedUser
     *
     * @param InvitedUser $invitedUser
     */
    public function removeInvitedUser(InvitedUser $invitedUser) {
        $this->invitedUsers->removeElement($invitedUser);
    }

    /**
     * Get invitedUsers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInvitedUsers() {
        return $this->invitedUsers;
    }

    /**
     * Add propertyRepostRequest
     *
     * @param PropertyRepostRequest $propertyRepostRequest
     *
     * @return Property
     */
    public function addPropertyRepostRequest(PropertyRepostRequest $propertyRepostRequest) {
        $this->propertyRepostRequests[] = $propertyRepostRequest;

        return $this;
    }

    /**
     * Remove propertyRepostRequest
     *
     * @param PropertyRepostRequest $propertyRepostRequest
     */
    public function removePropertyRepostRequest(PropertyRepostRequest $propertyRepostRequest) {
        $this->propertyRepostRequests->removeElement($propertyRepostRequest);
    }

    /**
     * Get propertyRepostRequests
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPropertyRepostRequests() {
        return $this->propertyRepostRequests;
    }

    /**
     * Set contractForm
     *
     * @param \Erp\PropertyBundle\Entity\ContractForm $contractForm
     *
     * @return Property
     */
    public function setContractForm(ContractForm $contractForm = null) {
        $this->contractForm = $contractForm;

        return $this;
    }

    /**
     * Get contractForm
     *
     * @return \Erp\PropertyBundle\Entity\ContractForm
     */
    public function getContractForm() {
        return $this->contractForm;
    }

    /**
     * Set applicationForm
     *
     * @param ApplicationForm $applicationForm
     *
     * @return Property
     */
    public function setApplicationForm(ApplicationForm $applicationForm = null) {
        $this->applicationForm = $applicationForm;

        return $this;
    }

    /**
     * Get applicationForm
     *
     * @return ApplicationForm
     */
    public function getApplicationForm() {
        return $this->applicationForm;
    }

    /**
     * Set settings
     *
     * @param \Erp\PropertyBundle\Entity\PropertySettings $settings
     *
     * @return Property
     */
    public function setSettings(PropertySettings $settings = null) {
        $this->settings = $settings;

        return $this;
    }

    /**
     * Get settings
     *
     * @return \Erp\PropertyBundle\Entity\PropertySettings
     */
    public function getSettings() {
        return $this->settings;
    }

    /**
     * Set settings
     *
     * @param \Erp\PropertyBundle\Entity\PropertySecurityDeposit $securityDeposit
     *
     * @return Property
     */
    public function setSecurityDeposit(PropertySecurityDeposit $securityDeposit = null) {
        $this->securityDeposit = $securityDeposit;

        return $this;
    }

    /**
     * Get settings
     *
     * @return \Erp\PropertyBundle\Entity\PropertySecurityDeposit
     */
    public function getSecurityDeposit() {
        return $this->securityDeposit;
    }

    /**
     * Set security deposit account
     *
     * @param \Erp\PaymentBundle\Entity\StripeDepositAccount $depositAccount
     *
     * @return Property
     */
    public function setDepositAccount(StripeDepositAccount $depositAccount = null) {
        $this->depositAccount = $depositAccount;

        return $this;
    }

    /**
     * Get security deposit account
     *
     * @return \Erp\PaymentBundle\Entity\StripeDepositAccount
     */
    public function getDepositAccount() {
        return $this->depositAccount;
    }

    /**
     * Add history
     *
     * @param \Erp\PropertyBundle\Entity\PropertyRentHistory $history
     *
     * @return Property
     */
    public function addHistory(PropertyRentHistory $history) {
        $history->setProperty($this);
        $this->history[] = $history;

        return $this;
    }

    /**
     * Remove history
     *
     * @param \Erp\PropertyBundle\Entity\PropertyRentHistory $history
     */
    public function removeHistory(PropertyRentHistory $history) {
        $this->history->removeElement($history);
    }

    /**
     * Get history
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHistory() {
        return $this->history;
    }

    public function isDeleted() {
        return $this->status === self::STATUS_DELETED;
    }

    public function isActive() {
        return !($this->isDeleted());
    }

    /**
     * @return \DateTime
     */
    public function getPaidDate() {
        return $this->paidDate;
    }

    /**
     * @param \DateTime $paidDate
     * @return Property
     */
    public function setPaidDate($paidDate) {
        $this->paidDate = $paidDate;
        return $this;
    }

    /**
     * Add transaction
     *
     * @param Transaction $transaction
     * @return Property
     */
    public function addTransaction(Transaction $transaction) {
        $transaction->setProperty($this);
        $this->transactions[] = $transaction;

        return $this;
    }

    /**
     * Remove transaction
     *
     * @param Transaction $transaction
     */
    public function removeTransaction(Transaction $transaction) {
        $this->transactions->removeElement($transaction);
    }

    /**
     * @return mixed
     */
    public function getTransactions() {
        return $this->transactions;
    }

    /**
     * Add ScheduledRentPayment
     *
     * @param ScheduledRentPayment $scheduledRentPayment
     * @return Property
     */
    public function addScheduledRentPayment(ScheduledRentPayment $scheduledRentPayment) {
        $scheduledRentPayment->setProperty($this);
        $this->scheduledRentPayments[] = $scheduledRentPayment;

        return $this;
    }

    /**
     * Remove ScheduledRentPayment
     *
     * @param ScheduledRentPayment $scheduledRentPayment
     */
    public function removeScheduledRentPayment(ScheduledRentPayment $scheduledRentPayment) {
        $this->transactions->removeElement($scheduledRentPayment);
    }

    /**
     * @return mixed
     */
    public function getScheduledRentPayments() {
        return $this->scheduledRentPayments;
    }

    /**
     * @param mixed $scheduledRentPayments
     */
    public function setScheduledRentPayments($scheduledRentPayments) {
        $this->scheduledRentPayments = $scheduledRentPayments;
    }

    /**
     * Set price
     *
     * @param float $price
     *
     * @return Property
     */
    public function setPrice($price) {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice() {
        return $this->price;
    }

}
