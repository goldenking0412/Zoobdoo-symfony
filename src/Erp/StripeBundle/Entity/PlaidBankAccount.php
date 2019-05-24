<?php

namespace Erp\StripeBundle\Entity;

class PlaidBankAccount implements PaymentTypeInterface
{
    /**
     * @var string
     */
    private $publicToken;

    /**
     * @var string
     */
    private $accountId;

    /**
     * @var string
     */
    private $bankAccountToken;

    /**
     * Get publicToken
     *
     * @return string
     */
    public function getPublicToken()
    {
        return $this->publicToken;
    }

    /**
     * Set publicToken
     *
     * @param string $publicToken
     *
     * @return PlaidBankAccount
     */
    public function setPublicToken($publicToken)
    {
        $this->publicToken = $publicToken;

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
     * Set accountId
     *
     * @param string $accountId
     *
     * @return PlaidBankAccount
     */
    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;

        return $this;
    }

    /**
     * Get bankAccountToken
     *
     * @return string
     */
    public function getBankAccountToken()
    {
        return $this->bankAccountToken;
    }

    /**
     * Set bankAccountToken
     *
     * @param string $bankAccountToken
     *
     * @return PlaidBankAccount
     */
    public function setBankAccountToken($bankAccountToken)
    {
        $this->bankAccountToken = $bankAccountToken;

        return $this;
    }

    public function toStripe()
    {
        throw new \RuntimeException();
    }

    public function getSourceToken()
    {
        return $this->bankAccountToken;
    }
}
