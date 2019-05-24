<?php
// TODO Remove in PaymentBundle
namespace Erp\StripeBundle\Entity;

interface PaymentTypeInterface
{
    /**
     * @return array
     */
    public function toStripe();

    /**
     * @return string
     */
    public function getSourceToken();
}
