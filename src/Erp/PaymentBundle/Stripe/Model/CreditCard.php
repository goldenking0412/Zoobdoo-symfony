<?php

namespace Erp\PaymentBundle\Stripe\Model;

class CreditCard implements PaymentTypeInterface {

    /**
     * @var string
     */
    protected $cartHolderFullName;

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
     * SetcartHolderFullName
     *
     * @param $cartHolderFullName
     *
     * @return CreditCard
     */
    public function setCartHolderFullName($cartHolderFullName) {
        $this->cartHolderFullName = $cartHolderFullName;

        return $this;
    }

    /**
     * Get cartHolderFullName
     *
     * @return string
     */
    public function getCartHolderFullName() {
        return $this->cartHolderFullName;
    }

    /**
     * Set number
     *
     * @param string $number
     *
     * @return CreditCard
     */
    public function setNumber($number) {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return string
     */
    public function getNumber() {
        return $this->number;
    }

    /**
     * Set expMonth
     *
     * @param string $expMonth
     *
     * @return CreditCard
     */
    public function setExpMonth($expMonth) {
        $this->expMonth = $expMonth;

        return $this;
    }

    /**
     * Get expMonth
     *
     * @return string
     */
    public function getExpMonth() {
        return $this->expMonth;
    }

    /**
     * Set expYear
     *
     * @param string $expYear
     *
     * @return CreditCard
     */
    public function setExpYear($expYear) {
        $this->expYear = $expYear;

        return $this;
    }

    /**
     * Get expYear
     *
     * @return string
     */
    public function getExpYear() {
        return $this->expYear;
    }

    /**
     * Set cvc
     * 
     * @param string $cvc
     * 
     * @return CreditCard
     */
    public function setCvc($cvc) {
        $this->cvc = $cvc;

        return $this;
    }

    /**
     * Get cvc
     *
     * @return string
     */
    public function getCvc() {
        return $this->cvc;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken() {
        return $this->token;
    }

    /**
     * Set token
     *
     * @param string $token
     *
     * @return CreditCard
     */
    public function setToken($token) {
        $this->token = $token;

        return $this;
    }

    public function toStripe() {
        return [
            'object' => 'card',
            'number' => $this->number,
            'name' => $this->cartHolderFullName,
            'exp_month' => $this->expMonth,
            'exp_year' => $this->expYear,
            'cvc' => $this->cvc,
        ];
    }

}
