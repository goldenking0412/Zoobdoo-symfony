<?php

namespace Erp\PaymentBundle\Stripe\Provider;

use Erp\PaymentBundle\Stripe\Manager\CustomerManager;
use Erp\PaymentBundle\Entity\StripeCustomer;
use Stripe\Customer;

class CreditCardPaymentTypeProvider implements PaymentTypeInterface
{
    /**
     * @var CustomerManager
     */
    private $customerManager;

    public function __construct(CustomerManager $customerManager)
    {
        $this->customerManager = $customerManager;
    }

    public function isSupportedType($type)
    {
        return $type === StripeCustomer::CREDIT_CARD;
    }

    public function createPayment(Customer $customer, array $params)
    {
        return $this->customerManager->createBankAccount($customer, $params);
    }

    public function updateIdField(StripeCustomer $stripeCustomer, $value)
    {
        $stripeCustomer->setCreditCardId($value);
    }
}