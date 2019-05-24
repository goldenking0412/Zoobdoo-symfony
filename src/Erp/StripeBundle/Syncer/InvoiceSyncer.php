<?php

namespace Erp\StripeBundle\Syncer;

use Stripe\ApiResource;

class InvoiceSyncer extends AbstractSyncer
{
    public function syncLocalFromStripe(ApiResource $stripeResource)
    {
    }
}

