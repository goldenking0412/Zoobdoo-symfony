<?php

namespace Erp\UserBundle\DataFixtures\ORM;

use Erp\CoreBundle\DataFixtures\ORM\EmailNotificationFixture;
use Erp\UserBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixture extends Fixture {

    /**
     * @inheritdoc
     */
    public function load(ObjectManager $manager) {
        $this->createManager();
        $this->createLandlord();
        $this->createTenant();
    }

    private function createManager() {
        $userManager = $this->container->get('fos_user.user_manager');
        $settings = $this->container->get('erp.users.user.service')->getSettings();
        $email = 'tonystark@test.com';

        /** @var User $user */
        $user = $userManager->findUserByEmail($email);
        if ($user instanceOf User) {
            //update current user
        } else {
            /** @var User $user */
            $user = $userManager->createUser();
        }

        $user->setRoles(array(User::ROLE_MANAGER));
        $user
                ->setCompanyName('My manager Company')
                ->setFirstName('Tony')
                ->setLastName('Stark')
                ->setPhone('555-555-5555')
                ->setAddressOne('Address One')
                ->setPostalCode('11111')
                ->setEmail($email)
                ->setPlainPassword('qweASD123')
                ->setEnabled(true)
                ->setUsername($user->getEmail())
                ->setStatus(User::STATUS_ACTIVE)
                ->setSettings(array_keys($settings))
                ->setPropertyCounter(User::DEFAULT_PROPERTY_COUNTER)
                ->setApplicationFormCounter(User::DEFAULT_APPLICATION_FORM_COUNTER)
                ->setContractFormCounter(User::DEFAULT_CONTRACT_FORM_COUNTER)
                ->setIsApplicationFormCounterFree(1)
                ->setIsPropertyCounterFree(1)
                ->setIsTermOfUse(true);

        $userManager->updateUser($user);

        $this->addReference('tonystark@test.com', $user);
    }

    private function createLandlord() {
        $userManager = $this->container->get('fos_user.user_manager');
        $settings = $this->container->get('erp.users.user.service')->getSettings();

        $email = 'johndoe@test.com';

        /** @var User $user */
        $user = $userManager->findUserByEmail($email);
        if ($user instanceOf User) {
            //update current user
        } else {
            /** @var User $user */
            $user = $userManager->createUser();
        }

        $user->setRoles(array(User::ROLE_LANDLORD));
        $user
                ->setCompanyName('My Landlord Company')
                ->setFirstName('John')
                ->setLastName('Doe')
                ->setPhone('111-111-1111')
                ->setAddressOne('Address One')
                ->setPostalCode('11111')
                ->setEmail($email)
                ->setPlainPassword('qweASD123')
                ->setEnabled(true)
                ->setUsername($user->getEmail())
                ->setStatus(User::STATUS_ACTIVE)
                ->setSettings(array_keys($settings))
                ->setPropertyCounter(User::DEFAULT_PROPERTY_COUNTER)
                ->setApplicationFormCounter(User::DEFAULT_APPLICATION_FORM_COUNTER)
                ->setContractFormCounter(User::DEFAULT_CONTRACT_FORM_COUNTER)
                ->setIsApplicationFormCounterFree(1)
                ->setIsPropertyCounterFree(1)
                ->setIsTermOfUse(true);

        $userManager->updateUser($user);

        $this->addReference('johndoe@test.com', $user);
    }

    private function createTenant() {
        $userManager = $this->container->get('fos_user.user_manager');

        $email = 'peterparker@test.com';
        /** @var User $user */
        $user = $userManager->findUserByEmail($email);
        if ($user instanceOf User) {
            //update current user
        } else {
            /** @var User $user */
            $user = $userManager->createUser();
        }

        $user->setRoles(array(User::ROLE_TENANT));
        $user
                ->setCompanyName('Tenant')
                ->setFirstName('Peter')
                ->setLastName('Parker')
                ->setPhone('555-555-5555')
                ->setAddressOne('Address One')
                ->setPostalCode('11111')
                ->setEmail($email)
                ->setPlainPassword('qweASD123')
                ->setEnabled(true)
                ->setUsername($user->getEmail())
                ->setStatus(User::STATUS_ACTIVE)
                ->setPropertyCounter(User::DEFAULT_PROPERTY_COUNTER)
                ->setApplicationFormCounter(User::DEFAULT_APPLICATION_FORM_COUNTER)
                ->setContractFormCounter(User::DEFAULT_CONTRACT_FORM_COUNTER)
                ->setIsTermOfUse(true);

        $userManager->updateUser($user);

        $this->addReference('peterparker@test.com', $user);
    }

    public function getDependencies() {
        return array(
            EmailNotificationFixture::class,
        );
    }

}
