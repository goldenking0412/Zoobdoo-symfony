<?php

namespace Erp\PaymentBundle\Stripe\Model;

interface PaymentTypeInterface
{
    /**
     * @return array
     */
    public function toStripe();
}