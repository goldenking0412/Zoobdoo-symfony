<?php
// TODO Remove in PaymentBundle
namespace Erp\StripeBundle\Entity;

class CreditCard implements PaymentTypeInterface
{
    /**
     * @var string
     */
    protected $firstName;

    /**
     * @var string
     */
    protected $middleName;

    /**
     * @var string
     */
    protected $lastName;

    /**
     * @var string
     */
    protected $number;

    /**
     * @var string
     */
    protected $expMonth;

    /**
     * @var string
     */
    protected $expYear;

    /**
     * @var string
     */
    protected $cvc;

    /**
     * @var string
     */
    protected $token;

    /**
     * Set firstName
     *
     * @param $firstName
     *
     * @return CreditCard
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
     * Set middleName
     *
     * @param $middleName
     *
     * @return CreditCard
     */
    public function setMiddleName($middleName)
    {
        $this->middleName = $middleName;

        return $this;
    }

    /**
     * Get middleName
     *
     * @return string
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * Set lastName
     *
     * @param $lastName
     *
     * @return CreditCard
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
     * Set number
     *
     * @param string $number
     *
     * @return CreditCard
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set expMonth
     *
     * @param string $expMonth
     *
     * @return CreditCard
     */
    public function setExpMonth($expMonth)
    {
        $this->expMonth = $expMonth;

        return $this;
    }

    /**
     * Get expMonth
     *
     * @return string
     */
    public function getExpMonth()
    {
        return $this->expMonth;
    }

    /**
     * Set expYear
     *
     * @param string $expYear
     *
     * @return CreditCard
     */
    public function setExpYear($expYear)
    {
        $this->expYear = $expYear;

        return $this;
    }

    /**
     * Get expYear
     *
     * @return string
     */
    public function getExpYear()
    {
        return $this->expYear;
    }

    /**
     * Set cvc
     *
     * @param string $cvc
     *
     * @return CreditCard
     */
    public function setCvc($cvc)
    {
        $this->cvc = $cvc;

        return $this;
    }

    /**
     * Get cvc
     *
     * @return string
     */
    public function getCvc()
    {
        return $this->cvc;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set token
     *
     * @param string $token
     *
     * @return CreditCard
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    public function toStripe()
    {
        return [
            'object' => 'card',
            'number' => $this->number,
            'exp_month' => $this->expMonth,
            'exp_year' => $this->expYear,
            'cvc' => $this->cvc,
        ];
    }

    public function getSourceToken()
    {
        return $this->token;
    }
}
