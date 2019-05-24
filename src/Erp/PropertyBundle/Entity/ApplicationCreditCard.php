<?php

namespace Erp\PropertyBundle\Entity;

use Erp\StripeBundle\Entity\CreditCard;

class ApplicationCreditCard {

    /**
     * @var CreditCard
     */
    private $creditCard;

    /**
     * @var string
     */
    private $email;

    /**
     * Set CreditCard
     *
     * @param CreditCard $creditCard
     *
     * @return $this
     */
    public function setCreditCard(CreditCard $creditCard) {
        $this->creditCard = $creditCard;

        return $this;
    }

    /**
     * Get creditCard
     *
     * @return CreditCard
     */
    public function getCreditCard() {
        return $this->creditCard;
    }

    /**
     * Set email
     *
     * @param $email
     *
     * @return $this
     */
    public function setEmail($email) {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

}
