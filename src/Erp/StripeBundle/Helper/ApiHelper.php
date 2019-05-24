<?php

namespace Erp\StripeBundle\Helper;

class ApiHelper
{
    public static function convertAmountToStripeFormat($amount)
    {
        return $amount * 100;
    }
}