<?php

namespace Erp\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Erp\UserBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ManagerFlagAssignFixture extends Fixture implements DependentFixtureInterface
{
    /**
     * @inheritdoc
     */
    public function load(ObjectManager $objectManager)
    {
        $userManager = $this->container->get('fos_user.user_manager');
        $landlord = $userManager->findUserByEmail('johndoe@test.com'); //landlord
        $landlord->setManager($this->getReference('tonystark@test.com'));//manager
        $userManager->updateUser($landlord);

        $tenant = $userManager->findUserByEmail('peterparker@test.com'); //tenant
        $tenant->setManager($this->getReference('johndoe@test.com'));//landlord
        $userManager->updateUser($landlord);
    }

    public function getDependencies()
    {
        return array(
            UserFixture::class,
        );
    }
}