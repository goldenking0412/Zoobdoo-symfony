<?php

namespace Erp\StripeBundle\DataFixtures\ORM;

use Erp\PaymentBundle\Entity\StripeAccount;
use Erp\PaymentBundle\Entity\StripeCustomer;
use Erp\StripeBundle\Entity\BalanceHistory;
use Erp\StripeBundle\Entity\Transaction;
use Erp\StripeBundle\Helper\ApiHelper;
use Erp\UserBundle\DataFixtures\ORM\ManagerFlagAssignFixture;
use Erp\UserBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class TransactionsFixture extends Fixture {

    /**
     * @inheritdoc
     */
    public function load(ObjectManager $objectManager) {
        /** @var User $manager */
        $manager = $this->getReference('tonystark@test.com');
        /** @var User $landlord */
        $landlord = $this->getReference('johndoe@test.com');

        $stripeAccount = $manager->getStripeAccount();
        if ($stripeAccount instanceof StripeAccount) {
            //stay exist account
        } else {
            $stripeAccount = new StripeAccount();
            $stripeAccount->setUser($manager);
        }


        $stripeAccount
                ->setAccountId('acct_1C35PQDhVopClpX5')//mock data
                ->setFirstName($manager->getFirstName())
                ->setLastName($manager->getLastName());
        $objectManager->persist($stripeAccount);
        $objectManager->flush();

        if ($landlord->hasStripeCustomers()) {
            $stripeCustomer = $landlord->getStripeCustomer();
        } else {
            $stripeCustomer = new StripeCustomer();
            $stripeCustomer->setUser($landlord);
        }

        $stripeCustomer
                ->setCustomerId('1234567890')//mock data
        ;
        $objectManager->persist($stripeCustomer);
        $objectManager->flush();

        $amount = ApiHelper::convertAmountToStripeFormat('100');
        $balance = ApiHelper::convertAmountToStripeFormat('100');

        $transaction = new Transaction();
        $transaction
                ->setAccount($stripeAccount)
                ->setCustomer($stripeCustomer)
                ->setCurrency('usd')
                ->setStatus(\Erp\StripeBundle\Event\ChargeEvent::SUCCEEDED)
                ->setCreated(new \DateTime())
                ->setAmount($amount)
                ->setBalance($balance)
                ->setPaymentMethod(Transaction::CREDIT_CARD_PAYMENT_METHOD)
                ->setType(Transaction::TYPE_CHARGE)
                ->setInternalType('сharge')
                ->setStripeId('ch_iHGJGsRYC4QA6u17FmtT') // mock data
        ;
        $transaction->setPaymentMethodDescription('Visa');

        $objectManager->persist($transaction);
        $objectManager->flush();

        $balanceHistory = new BalanceHistory();
        $balanceHistory->setTransaction($transaction);
        $objectManager->persist($balanceHistory);
        $balanceHistory
                ->setAmount($amount)
                ->setBalance($balance)
        ;
        $objectManager->persist($balanceHistory);
        $objectManager->flush();


        $transaction = new Transaction();
        $amount = ApiHelper::convertAmountToStripeFormat('150');
        $balance = ApiHelper::convertAmountToStripeFormat('250');
        $transaction
                ->setAccount($stripeAccount)
                ->setCustomer($stripeCustomer)
                ->setCurrency('usd')
                ->setStatus(\Erp\StripeBundle\Event\ChargeEvent::SUCCEEDED)
                ->setCreated(new \DateTime())
                ->setAmount($amount)
                ->setBalance($balance)
                ->setPaymentMethod(Transaction::CREDIT_CARD_PAYMENT_METHOD)
                ->setType(Transaction::TYPE_CHARGE)
                ->setInternalType('сharge')
                ->setStripeId('ch_SSXmpwc6CTzFo6me7JMM') // mock data
        ;
        $transaction->setPaymentMethodDescription('Visa');
        $objectManager->persist($transaction);
        $objectManager->flush();

        $balanceHistory = new BalanceHistory();
        $balanceHistory->setTransaction($transaction);
        $objectManager->persist($balanceHistory);
        $balanceHistory
                ->setAmount($amount)
                ->setBalance($balance)
        ;
        $objectManager->persist($balanceHistory);
        $objectManager->flush();

        $transaction = new Transaction();
        $amount = ApiHelper::convertAmountToStripeFormat('-100');
        $balance = ApiHelper::convertAmountToStripeFormat('150');
        $transaction
                ->setAccount($stripeAccount)
                ->setCustomer($stripeCustomer)
                ->setType(Transaction::TYPE_CHARGE)
                ->setCurrency('usd')
                ->setStatus(\Erp\StripeBundle\Event\ChargeEvent::SUCCEEDED)
                ->setCreated(new \DateTime())
                ->setAmount($amount)
                ->setBalance($balance)
                ->setPaymentMethod(Transaction::CREDIT_CARD_PAYMENT_METHOD)
                ->setInternalType('сharge')
                ->setStripeId('ch_1Cegso2eZvKYlo2Cd6FeBViW') // mock data
        ;
        $transaction->setPaymentMethodDescription('Visa');
        $objectManager->persist($transaction);
        $objectManager->flush();

        $balanceHistory = new BalanceHistory();
        $balanceHistory->setTransaction($transaction);
        $objectManager->persist($balanceHistory);
        $balanceHistory
                ->setAmount($amount)
                ->setBalance($balance)
        ;
        $objectManager->persist($balanceHistory);
        $objectManager->flush();
    }

    public function getDependencies() {
        return array(
            ManagerFlagAssignFixture::class,
        );
    }

}
