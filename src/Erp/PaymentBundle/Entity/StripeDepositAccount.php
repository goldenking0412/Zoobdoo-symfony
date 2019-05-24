<?php

namespace Erp\PaymentBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Erp\UserBundle\Entity\User;

/**
 * Class StripeDepositAccount
 *
 * @ORM\Table(name="stripe_deposit_account")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class StripeDepositAccount
{
    const DEFAULT_ACCOUNT_TYPE = 'custom';
    const DEFAULT_ACCOUNT_COUNTRY = 'US';
    const TYPE_COMPANY = 'company';
    const TYPE_INDIVIDUAL = 'individual';

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
     * @ORM\Column(name="account_id", type="string", nullable=true)
     */
    public $accountId;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", nullable=true)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="line1", type="string", nullable=true)
     */
    private $line1;

    /**
     * @var string
     *
     * @ORM\Column(name="postal_code", type="string", nullable=true)
     */
    private $postalCode;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", nullable=true)
     */
    private $state;

    /**
     * @var string
     *
     * @ORM\Column(name="business_name", type="string", nullable=true)
     */
    private $businessName;

    /**
     * @var string
     *
     * @ORM\Column(name="business_tax_id", type="string", nullable=true)
     */
    private $businessTaxId;

    /**
     * @var string
     *
     * @ORM\Column(name="day_of_birth", type="string", nullable=true)
     */
    private $dayOfBirth;

    /**
     * @var string
     *
     * @ORM\Column(name="month_of_birth", type="string", nullable=true)
     */
    private $monthOfBirth;

    /**
     * @var string
     *
     * @ORM\Column(name="year_of_birth", type="string", nullable=true)
     */
    private $yearOfBirth;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", nullable=true)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", nullable=true)
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="ssn_last4", type="string", nullable=true)
     */
    private $ssnLast4;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", nullable=true)
     */
    private $type = self::TYPE_INDIVIDUAL;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="tos_acceptance_date", type="date", nullable=true)
     */
    private $tosAcceptanceDate;

    /**
     * @var string
     *
     * @ORM\Column(name="tos_acceptance_ip", type="string", nullable=true)
     */
    private $tosAcceptanceIp;

    /**
     * @var string
     *
     * @ORM\Column(name="bank_account_id", type="string", nullable=true)
     */
    private $bankAccountId;

    /**
     * @var string
     *
     * @ORM\Column(name="bank_name", type="string", nullable=true)
     */
    private $bankName;

    /**
     * @var string
     *
     * @ORM\Column(name="account_holder_name", type="string", nullable=true)
     */
    private $accountHolderName;

    /**
     * @var string
     *
     * @ORM\Column(name="routing_number", type="string", nullable=true)
     */
    private $routingNumber;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;


    public function __construct()
    {
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime();
    }


    public function toStripe()
    {
        return [
            'legal_entity' => [
                'address' => [
                    'city' => $this->city,
                    'line1' => $this->line1,
                    'postal_code' => $this->postalCode,
                    'state' => $this->state,
                ],
                'business_name' => $this->businessName,
                'business_tax_id' => $this->businessTaxId,
                'dob' => [
                    'day' => $this->dayOfBirth,
                    'month' => $this->monthOfBirth,
                    'year' => $this->yearOfBirth,
                ],
                'first_name' => $this->firstName,
                'last_name' => $this->lastName,
                'ssn_last_4' => $this->ssnLast4,
                'type' => $this->type,
            ],
            'tos_acceptance' => [
                'date' => $this->tosAcceptanceDate->getTimestamp(),
                'ip' => $this->tosAcceptanceIp,
            ],
        ];
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
     * Set accountId
     *
     * @param string $accountId
     *
     * @return StripeDepositAccount
     */
    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;

        return $this;
    }

    /**
     * Get accountId
     *
     * @return string
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * Set line1
     *
     * @param string $line1
     *
     * @return StripeDepositAccount
     */
    public function setLine1($line1)
    {
        $this->line1 = $line1;

        return $this;
    }

    /**
     * Get line1
     *
     * @return string
     */
    public function getLine1()
    {
        return $this->line1;
    }

    /**
     * Set postalCode
     *
     * @param string $postalCode
     *
     * @return StripeDepositAccount
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * Get postalCode
     *
     * @return string
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * Set state
     *
     * @param string $state
     *
     * @return StripeDepositAccount
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set businessName
     *
     * @param string $businessName
     *
     * @return StripeDepositAccount
     */
    public function setBusinessName($businessName)
    {
        $this->businessName = $businessName;

        return $this;
    }

    /**
     * Get businessName
     *
     * @return string
     */
    public function getBusinessName()
    {
        return $this->businessName;
    }

    /**
     * Set businessTaxId
     *
     * @param string $businessTaxId
     *
     * @return StripeDepositAccount
     */
    public function setBusinessTaxId($businessTaxId)
    {
        $this->businessTaxId = $businessTaxId;

        return $this;
    }

    /**
     * Get businessTaxId
     *
     * @return string
     */
    public function getBusinessTaxId()
    {
        return $this->businessTaxId;
    }

    /**
     * Set dayOfBirth
     *
     * @param string $dayOfBirth
     *
     * @return StripeDepositAccount
     */
    public function setDayOfBirth($dayOfBirth)
    {
        $this->dayOfBirth = $dayOfBirth;

        return $this;
    }

    /**
     * Get dayOfBirth
     *
     * @return string
     */
    public function getDayOfBirth()
    {
        return $this->dayOfBirth;
    }

    /**
     * Set monthOfBirth
     *
     * @param string $monthOfBirth
     *
     * @return StripeDepositAccount
     */
    public function setMonthOfBirth($monthOfBirth)
    {
        $this->monthOfBirth = $monthOfBirth;

        return $this;
    }

    /**
     * Get monthOfBirth
     *
     * @return string
     */
    public function getMonthOfBirth()
    {
        return $this->monthOfBirth;
    }

    /**
     * Set yearOfBirth
     *
     * @param string $yearOfBirth
     *
     * @return StripeDepositAccount
     */
    public function setYearOfBirth($yearOfBirth)
    {
        $this->yearOfBirth = $yearOfBirth;

        return $this;
    }

    /**
     * Get yearOfBirth
     *
     * @return string
     */
    public function getYearOfBirth()
    {
        return $this->yearOfBirth;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return StripeDepositAccount
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
     * @return StripeDepositAccount
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
     * Set ssnLast4
     *
     * @param string $ssnLast4
     *
     * @return StripeDepositAccount
     */
    public function setSsnLast4($ssnLast4)
    {
        $this->ssnLast4 = $ssnLast4;

        return $this;
    }

    /**
     * Get ssnLast4
     *
     * @return string
     */
    public function getSsnLast4()
    {
        return $this->ssnLast4;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return StripeDepositAccount
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set tosAcceptanceDate
     *
     * @param \DateTime $tosAcceptanceDate
     *
     * @return StripeDepositAccount
     */
    public function setTosAcceptanceDate($tosAcceptanceDate)
    {
        $this->tosAcceptanceDate = $tosAcceptanceDate;

        return $this;
    }

    /**
     * Get tosAcceptanceDate
     *
     * @return \DateTime
     */
    public function getTosAcceptanceDate()
    {
        return $this->tosAcceptanceDate;
    }

    /**
     * Set tosAcceptanceIp
     *
     * @param string $tosAcceptanceIp
     *
     * @return StripeDepositAccount
     */
    public function setTosAcceptanceIp($tosAcceptanceIp)
    {
        $this->tosAcceptanceIp = $tosAcceptanceIp;

        return $this;
    }

    /**
     * Get tosAcceptanceIp
     *
     * @return string
     */
    public function getTosAcceptanceIp()
    {
        return $this->tosAcceptanceIp;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return StripeDepositAccount
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return StripeDepositAccount
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return StripeDepositAccount
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set bank_account_id
     *
     * @param string $bankAccountId
     *
     * @return StripeDepositAccount
     */
    public function setBankAccountId($bankAccountId)
    {
        $this->bankAccountId = $bankAccountId;

        return $this;
    }

    /**
     * Get bankAccountId
     *
     * @return string
     */
    public function getBankAccountId()
    {
        return $this->bankAccountId;
    }

    /**
     * Set account_holder_name
     *
     * @param string $accountHolderName
     *
     * @return StripeDepositAccount
     */
    public function setAccountHolderName($accountHolderName)
    {
        $this->accountHolderName = $accountHolderName;

        return $this;
    }

    /**
     * Get accountHolderName
     *
     * @return string
     */
    public function getAccountHolderName()
    {
        return $this->accountHolderName;
    }

    /**
     * Set bank_name
     *
     * @param string $bank_name
     *
     * @return StripeDepositAccount
     */
    public function setBankName($bankName)
    {
        $this->bankName = $bankName;

        return $this;
    }

    /**
     * Get accountHolderName
     *
     * @return string
     */
    public function getBankName()
    {
        return $this->bankName;
    }

    /**
     * Set routing_number
     *
     * @param string $routing_number
     *
     * @return StripeDepositAccount
     */
    public function setRoutingNumber($routingNumber)
    {
        $this->routingNumber = $routingNumber;

        return $this;
    }

    /**
     * Get accountHolderName
     *
     * @return string
     */
    public function getRoutingNumber()
    {
        return $this->routingNumber;
    }

}
