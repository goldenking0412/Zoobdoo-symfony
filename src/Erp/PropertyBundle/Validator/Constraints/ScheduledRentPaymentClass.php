<?php

namespace Erp\PropertyBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ScheduledRentPaymentClass extends Constraint
{
    public $dayUntilDueMessage = 'Rent due date {{ userValue }} is not accessible. You should pay only before {{ value }} day of the month.';
    public $paymentAmountMessage = 'Payment amount {{ value }} is not accessible.';
    public $allowAutoDraftMessage = 'Recurring payment is not allowed.';
    public $allowRentPaymentMessage = 'You can not pay for rent due to indebtedness.';

    public $depositAmountMessage = 'Payment Deposit amount {{ value }} is not accessible.';
    public $depositWantSecurityMessage = 'Security Deposit is not accessible.';

    /**
     * @inheritdoc
     */
    public function validatedBy()
    {
        return get_class($this).'Validator';
    }

    /**
     * @inheritdoc
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}