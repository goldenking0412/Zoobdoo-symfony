<?php

namespace Erp\PaymentBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Erp\PropertyBundle\Entity\ScheduledRentPayment;
use Erp\UserBundle\Entity\User;

/**
 * Class StripeCustomer
 *
 * @ORM\Table(name="stripe_customer")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class StripeCustomer {

    const BANK_ACCOUNT = 'ba';
    const CREDIT_CARD = 'cc';
    const BILLING_AUTOMATICALLY = 'charge_automatically';
    const BILLING_SEND_INVOICE = 'send_invoice';
    const BILLING_DAYS_UNTIL_DUE = 5;
    const DEFAULT_CURRENCY = 'usd';
    const TRIAL_PERIOD_DAYS = 30;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Erp\UserBundle\Entity\User", inversedBy="stripeCustomers", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="customer_id", type="string")
     */
    private $customerId;

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
     * @var StripeSubscription
     *
     * @ORM\OneToOne(targetEntity="Erp\PaymentBundle\Entity\StripeSubscription", mappedBy="stripeCustomer", cascade={"persist", "remove"})
     */
    protected $stripeSubscription;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="\Erp\StripeBundle\Entity\Invoice", mappedBy="customer", cascade={"persist"}, orphanRemoval=true)
     */
    private $invoices;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="\Erp\StripeBundle\Entity\Transaction", mappedBy="customer", cascade={"persist"}, orphanRemoval=true)
     */
    private $transactions;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="\Erp\PropertyBundle\Entity\ScheduledRentPayment", mappedBy="customer", cascade={"persist"}, orphanRemoval=true)
     */
    private $scheduledRentPayments;

    /**
     * @var string
     *
     * @ORM\Column(
     *      name="primary_type",
     *      length=2,
     *      type="string",
     *      nullable=true
     * )
     */
    protected $type = self::BANK_ACCOUNT;

    /**
     * Cosntructor
     */
    public function __construct() {
        $this->invoices = new ArrayCollection();
        $this->transactions = new ArrayCollection();
        $this->scheduledRentPayments = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist() {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * @ORM\PrePersist
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
     * Set customerId
     *
     * @param string $customerId
     *
     * @return StripeCustomer
     */
    public function setCustomerId($customerId) {
        $this->customerId = $customerId;

        return $this;
    }

    /**
     * Get customerId
     *
     * @return string
     */
    public function getCustomerId() {
        return $this->customerId;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return StripeCustomer
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
     * @return StripeCustomer
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
     * Set user
     *
     * @param \Erp\UserBundle\Entity\User $user
     *
     * @return StripeCustomer
     */
    public function setUser(\Erp\UserBundle\Entity\User $user = null) {
        $this->user = $user;

        $user->addStripeCustomer($this);

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
     * Set stripeSubscription
     *
     * @param string $stripeSubscription
     *
     * @return StripeCustomer
     */
    public function setStripeSubscription($stripeSubscription) {
        $this->stripeSubscription = $stripeSubscription;

        return $this;
    }

    /**
     * Get stripeSubscription
     *
     * @return StripeSubscription
     */
    public function getStripeSubscription() {
        return $this->stripeSubscription;
    }

    /**
     * Add transaction
     *
     * @param \Erp\StripeBundle\Entity\Transaction $transaction
     *
     * @return StripeCustomer
     */
    public function addTransaction(\Erp\StripeBundle\Entity\Transaction $transaction) {
        $this->transactions[] = $transaction;

        return $this;
    }

    /**
     * Remove transaction
     *
     * @param \Erp\StripeBundle\Entity\Transaction $transaction
     */
    public function removeTransaction(\Erp\StripeBundle\Entity\Transaction $transaction) {
        $this->transactions->removeElement($transaction);
    }

    /**
     * Get transactions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTransactions() {
        return $this->transactions;
    }

    /**
     * Add invoice
     *
     * @param \Erp\StripeBundle\Entity\Invoice $invoice
     *
     * @return StripeCustomer
     */
    public function addInvoice(\Erp\StripeBundle\Entity\Invoice $invoice) {
        $this->invoices[] = $invoice;

        return $this;
    }

    /**
     * Remove invoice
     *
     * @param \Erp\StripeBundle\Entity\Invoice $invoice
     */
    public function removeInvoice(\Erp\StripeBundle\Entity\Invoice $invoice) {
        $this->invoices->removeElement($invoice);
    }

    /**
     * Get invoices
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInvoices() {
        return $this->invoices;
    }

    /**
     * Add scheduledRentPayment
     *
     * @param \Erp\PropertyBundle\Entity\ScheduledRentPayment $scheduledRentPayment
     *
     * @return StripeCustomer
     */
    public function addScheduledRentPayment(\Erp\PropertyBundle\Entity\ScheduledRentPayment $scheduledRentPayment) {
        $this->scheduledRentPayments[] = $scheduledRentPayment;

        return $this;
    }

    /**
     * Remove scheduledRentPayment
     *
     * @param \Erp\PropertyBundle\Entity\ScheduledRentPayment $scheduledRentPayment
     */
    public function removeScheduledRentPayment(\Erp\PropertyBundle\Entity\ScheduledRentPayment $scheduledRentPayment) {
        $this->scheduledRentPayments->removeElement($scheduledRentPayment);
    }

    /**
     * Get scheduledRentPayments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getScheduledRentPayments() {
        return $this->scheduledRentPayments;
    }

    public function clearScheduledRentPayments() {
        $this->scheduledRentPayments->clear();
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return $this
     */
    public function setType($type) {
        $this->type = $type;

        return $this;
    }

    /**
     * Get primaryType
     *
     * @return string
     */
    public function getType() {
        return $this->type;
    }

}
