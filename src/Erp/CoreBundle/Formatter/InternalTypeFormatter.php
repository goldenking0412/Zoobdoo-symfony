<?php

namespace Erp\CoreBundle\Formatter;

use Erp\StripeBundle\Entity\Transaction;
use Erp\PropertyBundle\Entity\PropertySecurityDeposit;

class InternalTypeFormatter {

    public function format($value) {
        if (null === $value || '' === $value) {
            return $value;
        }

        $result = '';
        switch ($value) {
            case Transaction::INTERNAL_TYPE_CHARGE:
                $result = "Charge";
                break;
            case Transaction::INTERNAL_TYPE_RENT_PAYMENT:
                $result = "Rent payment";
                break;
            case Transaction::INTERNAL_TYPE_LATE_RENT_PAYMENT:
                $result = "Late rent payment";
                break;
            case Transaction::INTERNAL_TYPE_TENANT_SCREENING:
                $result = "Tenant Screening";
                break;
            case Transaction::INTERNAL_TYPE_ANNUAL_SERVICE_FEE:
                $result = "Annual Service Fee";
                break;
            case Transaction::INTERNAL_TYPE_PAY_LANDLORD:
                $result = "Payment to Landlord";
                break;
            case PropertySecurityDeposit::STATUS_DEPOSIT_PAID:
                $result = "Deposit paid";
                break;
            case PropertySecurityDeposit::STATUS_DEPOSIT_REFUNDED_NO:
                $result = "Deposit not refunded";
                break;
            case PropertySecurityDeposit::STATUS_DEPOSIT_REFUNDED_PARTIAL:
                $result = "Deposit partially refunded";
                break;
            case PropertySecurityDeposit::STATUS_DEPOSIT_REFUNDED_TOTAL:
                $result = "Deposit totally refunded";
                break;
            case PropertySecurityDeposit::STATUS_DEPOSIT_UNPAID:
                $result = "Deposit not paid";
                break;
        }

        return $result;
    }

}
