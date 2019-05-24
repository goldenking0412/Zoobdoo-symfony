<?php

namespace Erp\CoreBundle\Formatter;

use Erp\PropertyBundle\Entity\PropertySecurityDeposit;

class TransactionStatusFormatter
{
    public function format($value)
    {
        if (null === $value || '' === $value) {
            return $value;
        }

        $result = '';
        switch ($value) {
            case "succeeded":
                $result = "Cleared";
                break;
            case "pending":
                $result = "Pending";
                break;
            case "failed":
                $result = "Failed";
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
