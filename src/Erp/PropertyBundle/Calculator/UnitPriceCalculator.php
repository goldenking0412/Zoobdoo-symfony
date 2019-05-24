<?php

namespace Erp\PropertyBundle\Calculator;

use Doctrine\Common\Persistence\ManagerRegistry;

class UnitPriceCalculator {

    private $registry;

    public function __construct(ManagerRegistry $registry) {
        $this->registry = $registry;
    }

    public function calculate($settings, $quantity) {
        $amount = 0;
        foreach ($settings as $setting) {
            for ($i = $setting['min']; $i <= $quantity; $i++) {
                $amount += $setting['amount'];

                if ($i == $setting['max']) {
                    break;
                }
            }
        }

        return $amount;
    }

}
