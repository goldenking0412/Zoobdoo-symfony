<?php

namespace Erp\StripeBundle\Event;

class RefundEvent extends AbstractEvent {

    const REFUNDED = 'charge.refunded';
    const REFUNDED_UPDATED = 'charge.refund.updated';
    const SUCCEEDED = 'charge.succeeded';

}
