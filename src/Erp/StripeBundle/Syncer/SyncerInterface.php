<?php

namespace Erp\StripeBundle\Syncer;

use Stripe\ApiResource;

interface SyncerInterface
{
    public function syncLocalFromStripe(ApiResource $stripeResource);
}
