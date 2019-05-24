<?php

namespace Erp\PropertyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class PropertySetting
 *
 * @ORM\Table(name="properties_settings")
 * @ORM\Entity
 */
class PropertySettings {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Property
     *
     * @ORM\OneToOne(
     *      targetEntity="\Erp\PropertyBundle\Entity\Property",
     *      inversedBy="settings"
     * )
     * @ORM\JoinColumn(
     *      name="property_id",
     *      referencedColumnName="id",
     *      onDelete="CASCADE"
     * )
     */
    protected $property;

    /**
     * @var integer
     *
     * @ORM\Column(name="day_until_due", type="integer", nullable=true)
     */
    private $dayUntilDue;

    /**
     * @var integer
     *
     * @ORM\Column(name="move_in_date", type="date", nullable=true)
     */
    private $moveInDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="lease_end", type="date", nullable=true)
     */
    private $leaseEnd;

    /**
     * @var float
     *
     * @ORM\Column(name="payment_amount", type="float", nullable=true)
     */
    private $paymentAmount;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_allow_partial_payments", type="boolean", nullable=true)
     */
    private $allowPartialPayments;

    /**
     * @var boolean
     *
     * @ORM\Column(name="term_lease", type="boolean", nullable=true)
     */
    private $termLease;

    /**
     * @var boolean
     *
     * @ORM\Column(name="at_will", type="boolean", nullable=true)
     */
    private $atWill;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_allow_credit_card_payments", type="boolean", nullable=true)
     */
    private $allowCreditCardPayments;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_allow_auto_draft", type="boolean", nullable=true)
     */
    private $allowAutoDraft;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set dayUntilDue
     *
     * @param integer $dayUntilDue
     *
     * @return PropertySettings
     */
    public function setDayUntilDue($dayUntilDue) {
        $this->dayUntilDue = $dayUntilDue;

        return $this;
    }

    /**
     * Get dayUntilDue
     *
     * @return integer
     */
    public function getDayUntilDue() {
        return $this->dayUntilDue;
    }

    /**
     * Get moveInDate
     *
     * @return date
     */
    public function getMoveInDate() {
        return $this->moveInDate;
    }

    /**
     * Set moveInDate
     *
     * @param date $moveInDate
     *
     * @return PropertySettings
     */
    public function setMoveInDate($moveInDate) {
        $this->moveInDate = $moveInDate;

        return $this;
    }

    /**
     * Set leaseEnd
     *
     * @param date $leaseEnd
     *
     * @return PropertySettings
     */
    public function setLeaseEnd($leaseEnd) {
        $this->leaseEnd = $leaseEnd;

        return $this;
    }

    /**
     * Get leaseEnd
     *
     * @return date
     */
    public function getLeaseEnd() {
        return $this->leaseEnd;
    }

    /**
     * Set paymentAmount
     *
     * @param float $paymentAmount
     *
     * @return PropertySettings
     */
    public function setPaymentAmount($paymentAmount) {
        $this->paymentAmount = $paymentAmount;

        return $this;
    }

    /**
     * Get paymentAmount
     *
     * @return float
     */
    public function getPaymentAmount() {
        return $this->paymentAmount;
    }

    /**
     * Set allowPartialPayments
     *
     * @param boolean $allowPartialPayments
     *
     * @return PropertySettings
     */
    public function setAllowPartialPayments($allowPartialPayments) {
        $this->allowPartialPayments = (bool) $allowPartialPayments;

        return $this;
    }

    /**
     * Get allowPartialPayments
     *
     * @return boolean
     */
    public function getAllowPartialPayments() {
        return $this->allowPartialPayments;
    }

    /**
     * Set atWill
     *
     * @param boolean $atWill
     *
     * @return PropertySettings
     */
    public function setAtWill($atWill) {
        $this->atWill = $atWill;

        return $this;
    }

    /**
     * Get atWill
     *
     * @return boolean
     */
    public function getAtWill() {
        return $this->atWill;
    }

    /**
     * Set termLease
     *
     * @param boolean $termLease
     *
     * @return PropertySettings
     */
    public function setTermLease($termLease) {
        $this->termLease = $termLease;

        return $this;
    }

    /**
     * Get atWill
     *
     * @return boolean
     */
    public function getTermLease() {
        return $this->termLease;
    }

    /**
     * Set allowCreditCardPayments
     *
     * @param boolean $allowCreditCardPayments
     *
     * @return PropertySettings
     */
    public function setAllowCreditCardPayments($allowCreditCardPayments) {
        $this->allowCreditCardPayments = $allowCreditCardPayments;

        return $this;
    }

    /**
     * Get allowCreditCardPayments
     *
     * @return boolean
     */
    public function getAllowCreditCardPayments() {
        return $this->allowCreditCardPayments;
    }

    /**
     * Set allowAutoDraft
     *
     * @param boolean $allowAutoDraft
     *
     * @return PropertySettings
     */
    public function setAllowAutoDraft($allowAutoDraft) {
        $this->allowAutoDraft = (bool) $allowAutoDraft;

        return $this;
    }

    /**
     * Get allowAutoDraft
     *
     * @return boolean
     */
    public function getAllowAutoDraft() {
        return $this->allowAutoDraft;
    }

    public function isAllowAutoDraft() {
        return $this->allowAutoDraft;
    }

    /**
     * @return Property
     */
    public function getProperty() {
        return $this->property;
    }

    /**
     * @param Property $property
     */
    public function setProperty($property) {
        $this->property = $property;
    }

    /**
     * @return bool
     */
    public function isAllowPartialPayments() {
        return $this->allowPartialPayments;
    }

    /**
     * @return bool
     */
    public function isRestrictPartialPayments() {
        return !$this->allowPartialPayments;
    }

    public function replace(PropertySettings $settings) {
        $this->dayUntilDue = $settings->getDayUntilDue();
        $this->leaseEnd = $settings->getLeaseEnd();
        $this->atWill = $settings->getAtWill();
        $this->termLease = $settings->getTermLease();
        $this->paymentAmount = $settings->getPaymentAmount();
        $this->allowPartialPayments = $settings->getAllowPartialPayments();
        $this->allowCreditCardPayments = $settings->getAllowCreditCardPayments();
        $this->allowAutoDraft = $settings->getAllowAutoDraft();
    }

}
