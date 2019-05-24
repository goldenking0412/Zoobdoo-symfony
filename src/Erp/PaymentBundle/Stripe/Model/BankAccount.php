<?php

namespace Erp\PaymentBundle\Stripe\Model;

class BankAccount implements PaymentTypeInterface
{
    /**
     * @var string
     */
    protected $accountHolderName;

    /**
     * @var string;
     */
    protected $accountHolderType;

    /**
     * @var string
     */
    protected $accountNumber;

    /**
     * @var string
     */
    protected $country;

    /**
     * @var string
     */
    protected $currency;

    /**
     * @var string
     */
    protected $routingNumber;

    /**
     * Set accountHolderName
     *
     * @param $accountHolderName
     *
     * @return BankAccount
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
     * Get accountHolderType
     *
     * @return string
     */
    public function getAccountHolderType()
    {
        return $this->accountHolderType;
    }

    /**
     * Set accountHolderType
     *
     * @param string $accountHolderType
     *
     * @return BankAccount
     */
    public function setAccountHolderType($accountHolderType)
    {
        $accountHolderTypes = ['individual', 'company'];
        if (!in_array($accountHolderType,$accountHolderTypes)) {
            throw new \LogicException(sprintf('Invalid account holder type. Available %s', implode(', ', $accountHolderTypes)));
        }

        $this->accountHolderType = $accountHolderType;

        return $this;
    }

    /**
     * Set accountNumber
     *
     * @param $accountNumber
     *
     * @return BankAccount
     */
    public function setAccountNumber($accountNumber)
    {
        $this->accountNumber = $accountNumber;

        return $this;
    }

    /**
     * Get accountNumber
     *
     * @return string
     */
    public function getAccountNumber()
    {
        return $this->accountNumber;
    }

    /**
     * Set currency
     *
     * @param string $currency
     *
     * @return BankAccount
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set country
     *
     * @param $country
     *
     * @return BankAccount
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Get routingNumber
     *
     * @return string
     */
    public function getRoutingNumber()
    {
        return $this->routingNumber;
    }

    /**
     * Set routingNumber
     *
     * @param string $routingNumber
     *
     * @return BankAccount
     */
    public function setRoutingNumber($routingNumber)
    {
        $this->routingNumber = $routingNumber;

        return $this;
    }

    public function toStripe()
    {
        return [
            'account_holder_name' => $this->accountHolderName,
            'account_holder_type' => $this->accountHolderType,
            'account_number' => $this->accountNumber,
            'country' => $this->country,
            'currency' => $this->currency,
            'routing_number' => $this->routingNumber,
        ];
    }
}