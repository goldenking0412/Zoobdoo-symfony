<?php

namespace Erp\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class StripeSubscription
 *
 * @ORM\Table(name="stripe_subscription")
 * @ORM\Entity(repositoryClass="Erp\StripeBundle\Repository\SubscriptionRepository")
 * @ORM\HasLifecycleCallbacks
 */
class StripeSubscription {

    const YEARLY_PLAN_ID = 'base_yearly_plan';
    const MONTHLY_PLAN_ID = 'monthly_plan';
    const DEFAULT_CURRENCY = 'usd';
    const DEFAULT_INTERVAL = 'year';
    const BILLING_AUTOMATICALLY = 'charge_automatically';
    const BILLING_SEND_INVOICE = 'send_invoice';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var StripeCustomer
     *
     * @ORM\OneToOne(targetEntity="Erp\PaymentBundle\Entity\StripeCustomer", inversedBy="stripeSubscription")
     * @ORM\JoinColumn(name="stripe_customer_id", referencedColumnName="id")
     */
    private $stripeCustomer;

    /**
     * @var string
     *
     * @ORM\Column(name="subscription_id", type="string")
     */
    private $subscriptionId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="trial_period_start_at", type="datetime", nullable=true)
     */
    private $trialPeriodStartAt;

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
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set subscriptionId
     *
     * @param string $subscriptionId
     *
     * @return StripeSubscription
     */
    public function setSubscriptionId($subscriptionId) {
        $this->subscriptionId = $subscriptionId;

        return $this;
    }

    /**
     * Get subscriptionId
     *
     * @return string
     */
    public function getSubscriptionId() {
        return $this->subscriptionId;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return StripeSubscription
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
     * @return StripeSubscription
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
     * Set trialPeriodStartAt
     *
     * @param \DateTime $trialPeriodStartAt
     *
     * @return StripeSubscription
     */
    public function setTrialPeriodStartAt($trialPeriodStartAt) {
        $this->trialPeriodStartAt = $trialPeriodStartAt;

        return $this;
    }

    /**
     * Get trialPeriodStartAt
     *
     * @return \DateTime
     */
    public function getTrialPeriodStartAt() {
        return $this->trialPeriodStartAt;
    }

    /**
     * Set stripeCustomer
     *
     * @param \Erp\PaymentBundle\Entity\StripeCustomer $stripeCustomer
     *
     * @return StripeSubscription
     */
    public function setStripeCustomer(\Erp\PaymentBundle\Entity\StripeCustomer $stripeCustomer = null) {
        $this->stripeCustomer = $stripeCustomer;
        
        if ($stripeCustomer) {
            $stripeCustomer->setStripeSubscription($this);
        }

        return $this;
    }

    /**
     * Get stripeCustomer
     *
     * @return \Erp\PaymentBundle\Entity\StripeCustomer
     */
    public function getStripeCustomer() {
        return $this->stripeCustomer;
    }

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

}
