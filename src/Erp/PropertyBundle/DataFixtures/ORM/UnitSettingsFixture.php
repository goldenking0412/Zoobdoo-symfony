<?php

namespace Erp\PropertyBundle\DataFixtures\ORM;

use Erp\PropertyBundle\Entity\UnitSettings;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class UnitSettingsFixture
 *
 * In case if Docker doesn't install. See php docker-entrypoint.sh
 */
class UnitSettingsFixture extends Fixture
{
    /**
     * @inheritdoc
     */
    public function load(ObjectManager $manager)
    {
        $object = new UnitSettings();
        $object->setInitialQuantity(UnitSettings::DEFAULT_INITIAL_QUANTITY)
            ->setQuantityPerUnit(UnitSettings::DEFAULT_QUANTITY_PER_UNIT);

        $manager->persist($object);
        $manager->flush();
    }
}