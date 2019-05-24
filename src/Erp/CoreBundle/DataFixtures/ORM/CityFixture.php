<?php

namespace Erp\PropertyBundle\DataFixtures\ORM;

use Erp\CoreBundle\Entity\City;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class CityFixture extends Fixture
{
    /**
     * @inheritdoc
     */
    public function load(ObjectManager $manager)
    {

        $citiesJSON = file_get_contents('src/Erp/CoreBundle/DataFixtures/JSON/cities.json');
        $cities = json_decode($citiesJSON);

        foreach ($cities as $cityItem) {
            $city = new City();
            $city->setName($cityItem->name);
            $city->setStateCode($cityItem->state_code);
            $city->setZip($cityItem->zip);
            $city->setLatitude($cityItem->latitude);
            $city->setLongitude($cityItem->longitude);
            $city->setCountry($cityItem->country);
            $manager->persist($city);
        }
        $manager->flush();
    }
}