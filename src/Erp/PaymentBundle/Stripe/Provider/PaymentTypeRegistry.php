<?php

namespace Erp\PaymentBundle\Stripe\Provider;

class PaymentTypeRegistry
{
    private $providers = [];

    public function __construct(array $providers = [])
    {
        $this->providers = $providers;
    }

    /**
     * @return PaymentTypeInterface[]
     */
    public function getProviders()
    {
        return $this->providers;
    }
}