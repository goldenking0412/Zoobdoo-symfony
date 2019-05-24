<?php

namespace Erp\CoreBundle\Formatter;

class MoneyFormatter
{
    const DEFAULT_CURRENCY = 'USD';

    public function format($value)
    {
        if (null === $value || '' === $value) {
            return $value;
        }

        $format = new \NumberFormatter(\Locale::getDefault(), \NumberFormatter::CURRENCY);

        return $format->formatCurrency($value, self::DEFAULT_CURRENCY);
    }
}
