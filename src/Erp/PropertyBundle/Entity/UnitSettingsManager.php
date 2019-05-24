<?php

namespace Erp\PropertyBundle\Entity;

use Doctrine\Common\Persistence\ManagerRegistry;

class UnitSettingsManager
{
    /**
     * @var ManagerRegistry
     */
    private $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function getSettings()
    {
        $repository = $this->getRepository();
        /** @var UnitSettings $settings */
        return $repository->getSettings();
    }

    public function getInitialQuantity()
    {
        /** @var UnitSettings $settings */
        $settings = $this->getSettings();

        return $settings->getInitialQuantity();
    }

    public function getQuantityPerUnit()
    {
        /** @var UnitSettings $settings */
        $settings = $this->getSettings();

        return $settings->getQuantityPerUnit();
    }

    public function getRepository()
    {
        return $this->registry->getManagerForClass(UnitSettings::class)->getRepository(UnitSettings::class);
    }

    public function getQuantity(Unit $unit)
    {
        $quantityPerUnit = $this->getQuantityPerUnit();
        $initialQuantity = $this->getInitialQuantity();
        $count = $unit->getCount();

        return $initialQuantity + $quantityPerUnit * ($count - 1);
    }
}
