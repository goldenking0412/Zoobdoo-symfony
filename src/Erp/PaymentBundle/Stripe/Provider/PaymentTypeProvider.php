<?php

namespace Erp\PaymentBundle\Stripe\Provider;

use Erp\PaymentBundle\Entity\StripeCustomer;
use Erp\PaymentBundle\Stripe\Exception\UnsupportedTypeException;
use Stripe\Customer;

class PaymentTypeProvider implements PaymentProviderInterface
{
    /**
     * @var PaymentTypeRegistry
     */
    private $paymentTypeRegistry;

    public function __construct(PaymentTypeRegistry $paymentTypeRegistry)
    {
        $this->paymentTypeRegistry = $paymentTypeRegistry;
    }

    public function isSupportedType($type)
    {
        foreach ($this->paymentTypeRegistry->getProviders() as $provider) {
            if ($provider->isSupportedType($type)) {
                return true;
            }
        }

        return false;
    }

    public function createPayment(Customer $customer, $type, array $params)
    {
        return $this->getProviderForType($type)->createPayment($customer, $params);
    }

    /**
     * @param $type
     * @return PaymentTypeInterface
     * @throws UnsupportedTypeException
     */
    protected function getProviderForType($type)
    {
        foreach ($this->paymentTypeRegistry->getProviders() as $provider) {
            if ($provider->isSupportedType($type)) {
                return $provider;
            }
        }

        throw new UnsupportedTypeException();
    }

    public function updateIdField(StripeCustomer $stripeCustomer, $type, $value)
    {
        $this->getProviderForType($type)->updateIdField($stripeCustomer, $value);
    }
}
