<?php

namespace Erp\PropertyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Erp\PropertyBundle\Validator\Constraints as Assert;
use Erp\UserBundle\Entity\User;

/**
 * Class ScheduledRentPayment
 *
 * @ORM\Table(name="scheduled_rent_payment")
 * @ORM\Entity(repositoryClass="Erp\PropertyBundle\Repository\ScheduledRentPaymentRepository")
 * @ORM\HasLifecycleCallbacks
 * @Assert\ScheduledRentPaymentClass
 */
class ScheduledRentPayment {

    const TYPE_SINGLE = 'single';
    const TYPE_RECURRING = 'recurring';
    const STATUS_PENDING = 'pending';
    const STATUS_FAILURE = 'failure';
    const STATUS_SUCCESS = 'success';
    const CATEGORY_RENT_PAYMENT = 'rent_payment';
    const CATEGORY_LATE_RENT_PAYMENT = 'late_rent_payment';
    const CATEGORY_DEPOSIT_PAYMENT = 'deposit_payment';
    const CATEGORY_FEE = 'fee';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \Erp\PaymentBundle\Entity\StripeCustomer
     *
     * @ORM\ManyToOne(targetEntity="Erp\PaymentBundle\Entity\StripeCustomer", inversedBy="scheduledRentPayments")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     */
    private $customer;

    /**
     * @var \Erp\PaymentBundle\Entity\StripeAccount
     *
     * @ORM\ManyToOne(targetEntity="Erp\PaymentBundle\Entity\StripeAccount", inversedBy="scheduledRentPayments")
     * @ORM\JoinColumn(name="account_id", referencedColumnName="id")
     */
    private $account;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float", scale=2, nullable=true)
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", columnDefinition="ENUM('single','recurring')", nullable=true)
     */
    private $type = self::TYPE_RECURRING;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", columnDefinition="ENUM('pending', 'failure', 'success')", nullable=true)
     */
    private $status = self::STATUS_PENDING;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_payment_at", type="date", nullable=true)
     */
    private $startPaymentAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="next_payment_at", type="date", nullable=true)
     */
    private $nextPaymentAt;

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

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_at", type="date" ,nullable=true)
     */
    private $endAt;

    /**
     * @var string
     *
     * @ORM\Column(name="category", type="string")
     */
    private $category;

    /**
     * @var User
     */
    private $user;

    /**
     * @var Property
     *
     * @ORM\ManyToOne(targetEntity="Erp\PropertyBundle\Entity\Property", inversedBy="scheduledRentPayments")
     * @ORM\JoinColumn(name="property_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $property;

    /**
     * @ORM\PrePersist
     */
    public function prePersist() {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate() {
        $this->updatedAt = new \DateTime();
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
     * Set amount
     *
     * @param float $amount
     *
     * @return ScheduledRentPayment
     */
    public function setAmount($amount) {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount() {
        return $this->amount;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return ScheduledRentPayment
     */
    public function setType($type) {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return ScheduledRentPayment
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
     * Set startPaymentAt
     *
     * @param \DateTime $startPaymentAt
     *
     * @return ScheduledRentPayment
     */
    public function setStartPaymentAt($startPaymentAt) {
        $this->startPaymentAt = $startPaymentAt;

        return $this;
    }

    /**
     * Get startPaymentAt
     *
     * @return \DateTime
     */
    public function getStartPaymentAt() {
        return $this->startPaymentAt;
    }

    /**
     * Set nextPaymentAt
     *
     * @param \DateTime $nextPaymentAt
     *
     * @return ScheduledRentPayment
     */
    public function setNextPaymentAt($nextPaymentAt) {
        $this->nextPaymentAt = $nextPaymentAt;

        return $this;
    }

    /**
     * Get nextPaymentAt
     *
     * @return \DateTime
     */
    public function getNextPaymentAt() {
        return $this->nextPaymentAt;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return ScheduledRentPayment
     */
    public function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt() {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return ScheduledRentPayment
     */
    public function setUpdatedAt($updatedAt) {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt() {
        return $this->updatedAt;
    }

    /**
     * Set endAt
     *
     * @param \DateTime $endAt
     *
     * @return ScheduledRentPayment
     */
    public function setEndAt($endAt) {
        $this->endAt = $endAt;

        return $this;
    }

    /**
     * Get endAt
     *
     * @return \DateTime
     */
    public function getEndAt() {
        return $this->endAt;
    }

    /**
     * Set customer
     *
     * @param \Erp\PaymentBundle\Entity\StripeCustomer $customer
     *
     * @return ScheduledRentPayment
     */
    public function setCustomer(\Erp\PaymentBundle\Entity\StripeCustomer $customer = null) {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Get customer
     *
     * @return \Erp\PaymentBundle\Entity\StripeCustomer
     */
    public function getCustomer() {
        return $this->customer;
    }

    /**
     * Set account
     *
     * @param \Erp\PaymentBundle\Entity\StripeAccount $account
     *
     * @return ScheduledRentPayment
     */
    public function setAccount(\Erp\PaymentBundle\Entity\StripeAccount $account = null) {
        $this->account = $account;

        return $this;
    }

    /**
     * Get account
     *
     * @return \Erp\PaymentBundle\Entity\StripeAccount
     */
    public function getAccount() {
        return $this->account;
    }

    /**
     * Set category
     *
     * @param string category
     *
     * @return ScheduledRentPayment
     */
    public function setCategory($category) {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return string
     */
    public function getCategory() {
        return $this->category;
    }

    public function isRecurring() {
        return $this->type === self::TYPE_RECURRING;
    }

    /**
     * Set user
     *
     * @param $user
     *
     * @return ScheduledRentPayment
     */
    public function setUser($user) {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser() {
        return $this->user;
    }

    public function setAgreeAutoWithdrawal($agreeAutoWithdrawal) {
        if (!$this->user) {
            throw new \RuntimeException('Please specify user');
        }

        $this->user->setAgreeAutoWithdrawal($agreeAutoWithdrawal);
    }

    public function getAgreeAutoWithdrawal() {
        if (!$this->user) {
            return;
        }

        $this->user->getAgreeAutoWithdrawal();
    }

    /**
     * @return Property
     */
    public function getProperty() {
        return $this->property;
    }

    /**
     * @param Property $property
     * @return ScheduledRentPayment
     */
    public function setProperty($property) {
        $this->property = $property;
        return $this;
    }
    
    /**
     * 
     * @return boolean
     */
    public function isDepositPayment() {
        return ($this->category == self::CATEGORY_DEPOSIT_PAYMENT);
    }

}
