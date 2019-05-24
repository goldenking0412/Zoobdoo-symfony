<?php

namespace Erp\StripeBundle\Event;

class InvoiceEvent extends AbstractEvent
{
    const CREATED = 'invoice.created';
    const PAYMENT_FAILED = 'invoice.payment_failed';
    const PAYMENT_SUCCEEDED = 'invoice.payment_succeeded';
    const SENT = 'invoice.sent';
    const UPCOMING = 'invoice.upcoming';
    const UPDATED = 'invoice.updated';
}
