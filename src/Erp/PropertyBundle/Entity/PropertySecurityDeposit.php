<?php

namespace Erp\PropertyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PropertySecurityDeposit
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Erp\PropertyBundle\Repository\PropertySecurityDepositRepository")
 * @ORM\HasLifecycleCallbacks
 */
class PropertySecurityDeposit {

    const STATUS_DEPOSIT_UNPAID = 'unpaid';
    const STATUS_DEPOSIT_PAID = 'paid';
    const STATUS_DEPOSIT_REFUNDED_TOTAL = 'refunded_total';
    const STATUS_DEPOSIT_REFUNDED_PARTIAL = 'refunded_partial';
    const STATUS_DEPOSIT_REFUNDED_NO = 'refunded_no';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var boolean
     *
     * @ORM\Column(name="not_want_security_deposit", type="boolean", nullable=true))
     */
    private $notWantSecurityDeposit;

    /**
     * @var boolean
     *
     * @ORM\Column(name="yes_want_security_deposit", type="boolean", nullable=true))
     */
    private $yesWantSecurityDeposit;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float")
     */
    private $amount;

    /**
     * @var float
     *
     * @ORM\Column(name="refunded_amount", type="float", nullable=true)
     */
    private $refundedAmount;

    /**
     * @var integer
     *
     * @ORM\Column(name="send_to_main_account", type="boolean", nullable=true)
     */
    private $sendToMainAccount;

    /**
     * @var integer
     *
     * @ORM\Column(name="add_bank_account", type="boolean", nullable=true)
     */
    private $addBankAccount;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", nullable=true)
     */
    private $status;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="paid_at", type="datetime", nullable=true)
     */
    protected $paidAt;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="refunded_at", type="datetime", nullable=true)
     */
    protected $refundedAt;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set notWantSecurityDeposit
     *
     * @param boolean $notWantSecurityDeposit
     *
     * @return PropertySecurityDeposit
     */
    public function setNotWantSecurityDeposit($notWantSecurityDeposit) {
        $this->notWantSecurityDeposit = $notWantSecurityDeposit;

        return $this;
    }

    /**
     * Get notWantSecurityDeposit
     *
     * @return integer
     */
    public function getNotWantSecurityDeposit() {
        return $this->notWantSecurityDeposit;
    }

    /**
     * Set yesWantSecurityDeposit
     *
     * @param boolean $yesWantSecurityDeposit
     *
     * @return PropertySecurityDeposit
     */
    public function setYesWantSecurityDeposit($yesWantSecurityDeposit) {
        $this->yesWantSecurityDeposit = $yesWantSecurityDeposit;

        return $this;
    }

    /**
     * Get notWantSecurityDeposit
     *
     * @return boolean
     */
    public function getYesWantSecurityDeposit() {
        return $this->yesWantSecurityDeposit;
    }

    /**
     * Set amount
     *
     * @param float $amount
     *
     * @return PropertySecurityDeposit
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
     * Set refundedAmount
     *
     * @param float $refundedAmount
     *
     * @return PropertySecurityDeposit
     */
    public function setRefundedAmount($refundedAmount) {
        $this->refundedAmount = $refundedAmount;

        return $this;
    }

    /**
     * Get refundedAmount
     *
     * @return float
     */
    public function getRefundedAmount() {
        return $this->refundedAmount;
    }

    /**
     * Set sendToMainAccount
     *
     * @param boolean $sendToMainAccount
     *
     * @return PropertySecurityDeposit
     */
    public function setSendToMainAccount($sendToMainAccount) {
        $this->sendToMainAccount = $sendToMainAccount;

        return $this;
    }

    /**
     * Get sendToMainAccount
     *
     * @return boolean
     */
    public function getSendToMainAccount() {
        return $this->sendToMainAccount;
    }

    /**
     * Set addBankAccount
     *
     * @param boolean $addBankAccount
     *
     * @return PropertySecurityDeposit
     */
    public function setAddBankAccount($addBankAccount) {
        $this->addBankAccount = $addBankAccount;

        return $this;
    }

    /**
     * Get addBankAccount
     *
     * @return boolean
     */
    public function getAddBankAccount() {
        return $this->addBankAccount;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return PropertySecurityDeposit
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
     * @return \DateTime
     */
    public function getPaidAt() {
        return $this->paidAt;
    }

    /**
     * @return $this
     * 
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setPaidAt() {
        if ($this->status == self::STATUS_DEPOSIT_PAID) {
            $this->paidAt = new \DateTime();
        }
        
        return $this;
    }
    
    /**
     * @return \DateTime
     */
    public function getRefundedAt() {
        return $this->refundedAt;
    }

    /**
     * @return $this
     * 
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setRefundedAt() {
        if (($this->status == self::STATUS_DEPOSIT_REFUNDED_PARTIAL) || $this->status == self::STATUS_DEPOSIT_REFUNDED_TOTAL) {
            $this->refundedAt = new \DateTime();
        }

        return $this;
    }

}
