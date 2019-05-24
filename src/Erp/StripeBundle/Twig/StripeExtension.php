<?php

namespace Erp\StripeBundle\Twig;

use Erp\CoreBundle\Formatter\MoneyFormatter;

class StripeExtension extends \Twig_Extension {

    /**
     * @var MoneyFormatter
     */
    private $formatter;

    public function __construct(MoneyFormatter $formatter) {
        $this->formatter = $formatter;
    }

    /**
     * @return array
     */
    public function getFilters() {
        return array(
            new \Twig_SimpleFilter('stripe_money', array($this, 'formatMoney')),
        );
    }

    public function formatMoney($value, $divide = true) {
        if (null === $value || '' === $value) {
            return $value;
        }

        if ($divide) {
            $value = $value / 100;
        }

        return $this->formatter->format($value);
    }

}
