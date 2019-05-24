<?php

namespace Erp\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Fee
 *
 * @ORM\Table(name="fees_options")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class FeeOption {
    
    const REPOSITORY = 'ErpCoreBundle:FeeOption';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var float
     *
     * @ORM\Column(name="erentpay", type="float", options={"default" = 10})
     *
     * @Assert\NotBlank(message="Please enter Monthly Fee", groups={"updateOptions"})
     *
     * @Assert\GreaterThan(value = 0, message="Monthly Fee must be greater than 0", groups={"updateOptions"})
     */
    protected $erentpay;

    /**
     * @var float
     *
     * @ORM\Column(name="background_check", type="float", options={"default" = 1})
     *
     * @Assert\NotBlank(message="Please enter Tenant Screening Fee", groups={"updateOptions"})
     *
     * @Assert\GreaterThan(value = 0, message="Tenant Screening Fee must be greater than 0", groups={"updateOptions"})
     */
    protected $backgroundCheck;

    /**
     * @var float
     *
     * @Assert\NotBlank(message="Please enter Ask a Pro Fee", groups={"updateOptions"})
     *
     * @Assert\GreaterThan(value = 0, message="Ask a Pro Fee must be greater than 0", groups={"updateOptions"})
     *
     * @ORM\Column(name="ask_pro_check", type="float", options={"default" = 1})
     */
    protected $askProCheck;

    /**
     * @var float
     *
     * @ORM\Column(name="rent_allowance", type="float", options={"default" = 0.5})
     *
     * @Assert\NotBlank(message="Please enter Transaction Fee", groups={"updateOptions"})
     *
     * @Assert\GreaterThan(value = 0, message="Transaction Fee must be greater than 0",
     *                           groups={"updateOptions"})
     */
    protected $rentAllowance;

    /**
     * @var string
     *
     * @ORM\Column(name="default_email", type="string", length=255)
     *
     * @Assert\NotBlank(message="Please enter email", groups={"updateOptions"})
     *
     * @Assert\Length(
     *     min=6,
     *     max=255,
     *     minMessage="Email should have minimum 6 characters and maximum 255 characters",
     *     maxMessage="Email should have minimum 6 characters and maximum 255 characters",
     *     groups={"updateOptions"}
     * )
     *
     * @Assert\Email(message="This value is not a valid Email address. Use following formats: example@address.com",
     *              groups={"updateOptions"})
     */
    protected $defaultEmail;

    /**
     * @var float
     *
     * @Assert\NotBlank(message="Please enter Property Fee", groups={"updateOptions"})
     *
     * @Assert\GreaterThan(value = 0, message="Property Fee must be greater than 0", groups={"updateOptions"})
     *
     * @ORM\Column(name="property_fee", type="float", options={"default" = 1})
     */
    protected $propertyFee;

    /**
     * @var float
     *
     * @Assert\NotBlank(message="Please enter - Post Vacancy Online Fee", groups={"updateOptions"})
     *
     * @Assert\GreaterThan(
     *     value = 0,
     *     message="Post Vacancy Online Fee - must be greater than 0",
     *     groups={"updateOptions"}
     * )
     *
     * @ORM\Column(name="post_vacancy_online_fee", type="float", options={"default" = 1})
     */
    protected $postVacancyOnlineFee;

    /**
     * @var float
     *
     * @Assert\NotBlank(message="Please enter - Online Rental Application Form Fee", groups={"updateOptions"})
     *
     * @Assert\GreaterThan(
     *     value = 0,
     *     message="Online Rental Application Form Fee - must be greater than 0",
     *     groups={"updateOptions"}
     * )
     *
     * @ORM\Column(name="create_application_form_fee", type="float", options={"default" = 1})
     */
    protected $createApplicationFormFee;

    /**
     * @var float
     *
     * @Assert\NotBlank(message="Please enter - Online Rental Contract Form Fee", groups={"updateOptions"})
     *
     * @Assert\GreaterThan(
     *     value = 0,
     *     message="Online Rental Contract Form Fee - must be greater than 0",
     *     groups={"updateOptions"}
     * )
     *
     * @ORM\Column(name="create_contract_form_fee", type="float", options={"default" = 1})
     */
    protected $createContractFormFee;

    /**
     * @var float
     *
     * @Assert\NotBlank(message="Please enter - Check Payment Fee", groups={"updateOptions"})
     *
     * @Assert\GreaterThan(
     *     value = 0,
     *     message="Check Payment Fee - must be greater than 0",
     *     groups={"updateOptions"}
     * )
     *
     * @ORM\Column(name="check_payment_fee", type="float", options={"default" = 1})
     */
    protected $checkPaymentFee;

    /**
     * @var float
     *
     * @Assert\NotBlank(message="Please enter - CCard Transaction Fee (%)", groups={"updateOptions"})
     *
     * @Assert\GreaterThan(
     *     value = 0,
     *     message="CCard Transaction Fee - must be greater than 0",
     *     groups={"updateOptions"}
     * )
     *
     * @ORM\Column(name="cc_transaction_fee", type="float", options={"default" = 1})
     */
    protected $ccTransactionFee;

    /**
     * @var float
     *
     * @Assert\NotBlank(message="Please enter - ACH Transaction Fee (%)", groups={"updateOptions"})
     *
     * @Assert\GreaterThan(
     *     value = 0,
     *     message="ACH Transaction Fee - must be greater than 0",
     *     groups={"updateOptions"}
     * )
     *
     * @ORM\Column(name="ach_transaction_fee", type="float", options={"default" = 3})
     */
    protected $achTransactionFee;

    /**
     * @var float
     *
     * @Assert\NotBlank(message="Please enter - Online Rental Application Fee (Applicants)", groups={"updateOptions"})
     *
     * @Assert\GreaterThan(
     *     value = 0,
     *     message="Online Rental Application Fee (Applicants) - must be greater than 0",
     *     groups={"updateOptions"}
     * )
     *
     * @ORM\Column(name="application_form_anonymous_fee", type="float", options={"default" = 1})
     */
    protected $applicationFormAnonymousFee;

    /**
     * @var boolean
     *
     * @ORM\Column(name="smart_move_enable", type="boolean")
     */
    protected $smartMoveEnable = false;

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
     * Set erentpay
     *
     * @param float $erentpay
     *
     * @return $this
     */
    public function setErentpay($erentpay) {
        $this->erentpay = $erentpay;

        return $this;
    }

    /**
     * Get erentpay
     *
     * @return float
     */
    public function getErentpay() {
        return $this->erentpay;
    }

    /**
     * Set backgroundCheck
     *
     * @param float $backgroundCheck
     *
     * @return $this
     */
    public function setBackgroundCheck($backgroundCheck) {
        $this->backgroundCheck = $backgroundCheck;

        return $this;
    }

    /**
     * Get backgroundCheck
     *
     * @return float
     */
    public function getBackgroundCheck() {
        return $this->backgroundCheck;
    }

    /**
     * Set askProCheck
     *
     * @param float $askProCheck
     *
     * @return $this
     */
    public function setAskProCheck($askProCheck) {
        $this->askProCheck = $askProCheck;

        return $this;
    }

    /**
     * Get askProCheck
     *
     * @return float
     */
    public function getAskProCheck() {
        return $this->askProCheck;
    }

    /**
     * Set askProCheck
     *
     * @param float $rentAllowance
     *
     * @return $this
     */
    public function setRentAllowance($rentAllowance) {
        $this->rentAllowance = $rentAllowance;

        return $this;
    }

    /**
     * Get rentAllowance
     *
     * @return float
     */
    public function getRentAllowance() {
        return $this->rentAllowance;
    }

    /**
     * Set defaultEmail
     *
     * @param string $email
     *
     * @return $this
     */
    public function setDefaultEmail($email) {
        $this->defaultEmail = $email;

        return $this;
    }

    /**
     * Get defaultEmail
     *
     * @return string
     */
    public function getDefaultEmail() {
        return $this->defaultEmail;
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
     * Set propertyFee
     *
     * @param float $propertyFee
     *
     * @return FeeOption
     */
    public function setPropertyFee($propertyFee) {
        $this->propertyFee = $propertyFee;

        return $this;
    }

    /**
     * Get propertyFee
     *
     * @return float
     */
    public function getPropertyFee() {
        return $this->propertyFee;
    }

    /**
     * Set postVacancyOnlineFee
     *
     * @param float $postVacancyOnlineFee
     *
     * @return FeeOption
     */
    public function setPostVacancyOnlineFee($postVacancyOnlineFee) {
        $this->postVacancyOnlineFee = $postVacancyOnlineFee;

        return $this;
    }

    /**
     * Get postVacancyOnlineFee
     *
     * @return float
     */
    public function getPostVacancyOnlineFee() {
        return $this->postVacancyOnlineFee;
    }

    /**
     * Set createApplicationFormFee
     *
     * @param float $createApplicationFormFee
     *
     * @return FeeOption
     */
    public function setCreateApplicationFormFee($createApplicationFormFee) {
        $this->createApplicationFormFee = $createApplicationFormFee;

        return $this;
    }

    /**
     * Get createApplicationFormFee
     *
     * @return float
     */
    public function getCreateApplicationFormFee() {
        return $this->createApplicationFormFee;
    }

    /**
     * Set createContractFormFee
     *
     * @param float $createContractFormFee
     *
     * @return FeeOption
     */
    public function setCreateContractFormFee($createContractFormFee) {
        $this->createContractFormFee = $createContractFormFee;

        return $this;
    }

    /**
     * Get createContractFormFee
     *
     * @return float
     */
    public function getCreateContractFormFee() {
        return $this->createContractFormFee;
    }

    /**
     * Set smartMoveEnable
     *
     * @param boolean $smartMoveEnable
     *
     * @return FeeOption
     */
    public function setSmartMoveEnable($smartMoveEnable) {
        $this->smartMoveEnable = $smartMoveEnable;

        return $this;
    }

    /**
     * Get smartMoveEnable
     *
     * @return boolean
     */
    public function getSmartMoveEnable() {
        return $this->smartMoveEnable;
    }

    /**
     * Set checkPaymentFee
     *
     * @param float $checkPaymentFee
     *
     * @return FeeOption
     */
    public function setCheckPaymentFee($checkPaymentFee) {
        $this->checkPaymentFee = $checkPaymentFee;

        return $this;
    }

    /**
     * Get checkPaymentFee
     *
     * @return float
     */
    public function getCheckPaymentFee() {
        return $this->checkPaymentFee;
    }

    /**
     * Set ccTransactionFee
     *
     * @param float $ccTransactionFee
     *
     * @return FeeOption
     */
    public function setCcTransactionFee($ccTransactionFee) {
        $this->ccTransactionFee = $ccTransactionFee;

        return $this;
    }

    /**
     * Get ccTransactionFee
     *
     * @return float
     */
    public function getCcTransactionFee() {
        return $this->ccTransactionFee;
    }

    /**
     * Set achTransactionFee
     *
     * @param float $achTransactionFee
     *
     * @return FeeOption
     */
    public function setAchTransactionFee($achTransactionFee) {
        $this->achTransactionFee = $achTransactionFee;

        return $this;
    }

    /**
     * Get achTransactionFee
     *
     * @return float
     */
    public function getAchTransactionFee() {
        return $this->achTransactionFee;
    }

    /**
     * Set applicationFormAnonymousFee
     *
     * @param float $applicationFormAnonymousFee
     *
     * @return FeeOption
     */
    public function setApplicationFormAnonymousFee($applicationFormAnonymousFee) {
        $this->applicationFormAnonymousFee = $applicationFormAnonymousFee;

        return $this;
    }

    /**
     * Get applicationFormAnonymousFee
     *
     * @return float
     */
    public function getApplicationFormAnonymousFee() {
        return $this->applicationFormAnonymousFee;
    }

}
