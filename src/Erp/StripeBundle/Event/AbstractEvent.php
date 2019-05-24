<?php

namespace Erp\StripeBundle\Event;

use Symfony\Component\EventDispatcher\Event;

abstract class AbstractEvent extends Event
{
    /**
     * @var \Stripe\Event
     */
    private $stripeEvent;

    public function __construct(\Stripe\Event $stripeEvent)
    {
        $this->stripeEvent = $stripeEvent;
    }

    public function getStripeEvent()
    {
        return $this->stripeEvent;
    }
}
