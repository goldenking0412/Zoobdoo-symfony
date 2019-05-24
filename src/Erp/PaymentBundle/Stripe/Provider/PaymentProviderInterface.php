<?php

namespace Erp\PaymentBundle\Stripe\Provider;

use Stripe\Customer;

interface PaymentProviderInterface
{
    public function isSupportedType($type);

    public function createPayment(Customer $customer, $type, array $params);
}