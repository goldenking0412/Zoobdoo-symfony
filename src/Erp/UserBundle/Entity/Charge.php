<?php

namespace Erp\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Erp\CoreBundle\Entity\DatesAwareInterface;
use Erp\CoreBundle\Entity\DatesAwareTrait;
use Erp\StripeBundle\Entity\Transaction;

/**
 * Charge
 *
 * @ORM\Table(name="charges")
 * @ORM\Entity(repositoryClass="Erp\UserBundle\Repository\ChargeRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Charge implements DatesAwareInterface {

    use DatesAwareTrait;

    const STATUS_NEW = 'new';
    const STATUS_SENT = 'sent';
    const STATUS_PAID = 'paid';
    const STATUS_PENDING = 'pending';
    const STATUS_FAILURE = 'failure';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", columnDefinition="ENUM('new','sent','paid','pending','failure') DEFAULT 'new'")
     * 
     */
    protected $status = self::STATUS_NEW; // self::STATUS_PENDING;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_recurring", type="boolean")
     */
    protected $recurring = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="recurring_day_of_month", type="integer", nullable=true)
     */
    protected $recurringDayOfMonth;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="string", length=255)
     */
    protected $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @var User Manager
     * @ORM\ManyToOne(targetEntity="Erp\UserBundle\Entity\User", inversedBy="chargeOutgoings")
     */
    protected $manager; //sender

    /**
     * @var ArrayCollection
     * 
     * @ORM\ManyToOne(targetEntity="Erp\UserBundle\Entity\User", inversedBy="chargeIncomings")
     * @ORM\JoinColumn(
     *      name="receiver_id",
     *      referencedColumnName="id",
     *      onDelete="CASCADE"
     * )
     * @ORM\OrderBy({"createdDate"="ASC"})
     */
    protected $receiver; //receiver

    /**
     * @var Charge Parent
     * @ORM\ManyToOne(targetEntity="Erp\UserBundle\Entity\Charge", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $parent;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Erp\UserBundle\Entity\Charge", mappedBy="parent")
     */
    protected $children;

    /**
     * @var Transaction
     * 
     * @ORM\OneToOne(targetEntity="Erp\StripeBundle\Entity\Transaction", mappedBy="charge")
     * @ORM\JoinColumn(
     *      name="transaction_id",
     *      referencedColumnName="id",
     *      onDelete="CASCADE"
     * )
     */
    protected $transaction; //can be empty if not yet paid
    
    public $datTimeMail;
    public $txnId;

    /**
     * Constructor
     */

    public function __construct() {
        $this->children = new ArrayCollection();
    }

    /**
     * Constructor
     */
    public function __destruct() {
        unset($this->children);
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist() {
        $this->setCreatedAt(new \DateTime());
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function preUpdate() {
        $this->setUpdatedAt(new \DateTime());
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Charge
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
     * Set amount
     *
     * @param string $amount
     *
     * @return Charge
     */
    public function setAmount($amount) {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return string
     */
    public function getAmount() {
        return $this->amount;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Charge
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Set manager
     *
     * @param \Erp\UserBundle\Entity\User $manager
     *
     * @return Charge
     */
    public function setManager(\Erp\UserBundle\Entity\User $manager = null) {
        $this->manager = $manager;

        return $this;
    }

    /**
     * Get manager
     *
     * @return \Erp\UserBundle\Entity\User
     */
    public function getManager() {
        return $this->manager;
    }

    /**
     * @return bool
     */
    public function isPaid() {
        return $this->status === self::STATUS_PAID;
    }

    /**
     * @return bool
     */
    public function isRecurring() {
        return $this->recurring;
    }

    /**
     * Set recurring
     *
     * @param bool $recurring
     *
     * @return Charge
     */
    public function setRecurring($recurring) {
        $this->recurring = $recurring;

        return $this;
    }

    /**
     * Get recurring
     *
     * @return bool
     */
    public function getRecurring() {
        return $this->recurring;
    }

    /**
     * 
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt = null) {
        $this->createdAt = $createdAt;
    }

    /**
     * 
     * @return \DateTime
     */
    public function getCreatedAt() {
        return $this->createdAt;
    }

    /**
     * 
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt = null) {
        $this->updatedAt = $updatedAt;
    }

    /**
     * 
     * @return \DateTime
     */
    public function getUpdatedAt() {
        return $this->updatedAt;
    }

    /**
     * @return Transaction
     */
    public function getTransaction() {
        return $this->transaction;
    }

    /**
     * 
     * @param Transaction $transaction
     * @return Charge
     */
    public function setTransaction($transaction) {
        $this->transaction = $transaction;

        return $this;
    }

    /**
     * Set receiver
     *
     * @param \Erp\UserBundle\Entity\User $receiver
     *
     * @return Charge
     */
    public function setReceiver(\Erp\UserBundle\Entity\User $receiver) {
        $this->receiver = $receiver;

        return $this;
    }

    /**
     * Get receiver
     *
     * @return \Erp\UserBundle\Entity\User
     */
    public function getReceiver() {
        return $this->receiver;
    }

    /**
     * 
     * @param integer $recurringDayOfMonth | null
     * @return Charge
     */
    public function setRecurringDayOfMonth($recurringDayOfMonth = null) {
        if ($recurringDayOfMonth) {
            $this->recurringDayOfMonth = min(array(31, max(array(1, $recurringDayOfMonth))));

            if (!($this->isRecurring())) {
                $this->setRecurring(true);
            }
        } else {
            $this->setRecurring(false);
        }

        return $this;
    }

    /**
     * 
     * @return integer
     */
    public function getRecurringDayOfMonth() {
        return $this->recurringDayOfMonth;
    }

    /**
     * 
     * @param User $paidBy | null
     * @return Charge
     */
    public function setPaidBy(User $paidBy = null) {
        $this->paidBy = $paidBy;

        return $this;
    }

    /**
     * 
     * @return User
     */
    public function getPaidBy() {
        return $this->paidBy;
    }

    /**
     * Set parent
     *
     * @param \Erp\UserBundle\Entity\Charge $parent
     *
     * @return Charge
     */
    public function setParent(\Erp\UserBundle\Entity\Charge $parent = null) {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Erp\UserBundle\Entity\Charge
     */
    public function getParent() {
        return $this->parent;
    }

    /**
     * Add child
     *
     * @param \Erp\UserBundle\Entity\Charge $child
     *
     * @return Charge
     */
    public function addChild(\Erp\UserBundle\Entity\Charge $child) {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \Erp\UserBundle\Entity\Charge $child
     */
    public function removeChild(\Erp\UserBundle\Entity\Charge $child) {
        $this->children->removeElement($child);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren() {
        return $this->children;
    }

}
