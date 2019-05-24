<?php

namespace Erp\VendorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VendorEdit
 *
 * @ORM\Table(name="vendor_edit")
 * @ORM\Entity(repositoryClass="Erp\VendorBundle\Repository\VendorEditRepository")
 * @ORM\HasLifecycleCallbacks
 */
class VendorEdit {

    const REPOSITORY = 'ErpVendorBundle:VendorEdit';

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
     * @ORM\Column(name="company_name", type="string", length=255)
     */
    private $companyName;

    /**
     * @var string
     *
     * @ORM\Column(name="business_type", type="string", length=255)
     */
    private $businessType;

    /**
     * @var string
     *
     * @ORM\Column(name="website", type="string", length=255)
     */
    private $website;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255)
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="contact_email", type="string", length=255)
     */
    private $contactEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="contact_phone", type="string", length=255)
     */
    private $contactPhone;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_date", type="datetime")
     */
    protected $createdDate;

    /**
     * @ORM\ManyToOne(
     *      targetEntity="\Erp\VendorBundle\Entity\VendorCreate",
     *      inversedBy="vendorEdits"
     * )
     * @ORM\JoinColumn(
     *      name="vendor_create_id",
     *      referencedColumnName="id",
     *      onDelete="CASCADE"
     * )
     */
    protected $vendorCreate;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set companyName
     *
     * @param string $companyName
     *
     * @return VendorEdit
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
     * Set businessType
     *
     * @param string $businessType
     *
     * @return VendorEdit
     */
    public function setBusinessType($businessType) {
        $this->businessType = $businessType;

        return $this;
    }

    /**
     * Get businessType
     *
     * @return string
     */
    public function getBusinessType() {
        return $this->businessType;
    }

    /**
     * Set website
     *
     * @param string $website
     *
     * @return VendorEdit
     */
    public function setWebsite($website) {
        $this->website = $website;

        return $this;
    }

    /**
     * Get website
     *
     * @return string
     */
    public function getWebsite() {
        return $this->website;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return VendorEdit
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
     * Set firstName
     *
     * @param string $firstName
     *
     * @return VendorEdit
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
     * @return VendorEdit
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
     * Set email
     *
     * @param string $email
     *
     * @return VendorEdit
     */
    public function setContactEmail($email) {
        $this->contactEmail = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getContactEmail() {
        return $this->contactEmail;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return VendorEdit
     */
    public function setContactPhone($phone) {
        $this->contactPhone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getContactPhone() {
        return $this->contactPhone;
    }

    /**
     * Set vendorCreate
     *
     * @param \Erp\VendorBundle\Entity\VendorCreate $vendorCreate
     *
     * @return VendorEdit
     */
    public function setVendorCreate(\Erp\VendorBundle\Entity\VendorCreate $vendorCreate = null) {
        $this->vendorCreate = $vendorCreate;

        return $this;
    }

    /**
     * Get vendorCreate
     *
     * @return \Erp\VendorBundle\Entity\VendorCreate
     */
    public function getVendorCreate() {
        return $this->vendorCreate;
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

}
