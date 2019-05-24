<?php

namespace Erp\StripeBundle\Event;

class ChargeEvent extends AbstractEvent
{
    const CAPTURED = 'charge.captured';
    const FAILED = 'charge.failed';
    const PENDING = 'charge.pending';
    const REFUNDED = 'charge.refunded';
    const REFUNDED_UPDATED = 'charge.refund.updated';
    const SUCCEEDED = 'charge.succeeded';
    const UPDATED = 'charge.updated';
    const DISPUTE_CLOSED = 'charge.dispute.closed';
    const DISPUTE_CREATED = 'charge.dispute.created';
    const DISPUTE_FUNDS_REINSTATED = 'charge.dispute.funds_reinstated';
    const DISPUTE_FUNDS_WITHDRAWN = 'charge.dispute.funds_withdrawn';
    const DISPUTE_UPDATED = 'charge.dispute.updated';
}
