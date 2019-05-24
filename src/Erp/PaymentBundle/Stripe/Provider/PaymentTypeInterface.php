<?php

namespace Erp\PaymentBundle\Stripe\Provider;

use Stripe\Customer;
use Erp\PaymentBundle\Entity\StripeCustomer;
use Erp\PaymentBundle\Stripe\Client\Response;

interface PaymentTypeInterface
{
    /**
     * @param $type
     * @return bool
     */
    public function isSupportedType($type);

    /**
     * @param Customer $customer
     * @param array $params
     * @return Response
     */
    public function createPayment(Customer $customer, array $params);

    /**
     * @param StripeCustomer $stripeCustomer
     * @param $value
     * @return void
     */
    public function updateIdField(StripeCustomer $stripeCustomer, $value);
}