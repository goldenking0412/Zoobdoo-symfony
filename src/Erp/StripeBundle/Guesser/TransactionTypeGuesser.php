<?php

namespace Erp\StripeBundle\Guesser;

use Erp\StripeBundle\Entity\Transaction;

class TransactionTypeGuesser
{
    public function guess($type)
    {
        switch ($type) {
            case Transaction::CASH_IN:
                return [
                    Transaction::TYPE_CHARGE,
                ];
                break;
            case Transaction::CASH_OUT:
                return [
                    Transaction::TYPE_CHARGE,
                ];
                break;
        }
    }
}