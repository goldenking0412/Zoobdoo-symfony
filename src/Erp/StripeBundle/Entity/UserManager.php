<?php

namespace Erp\StripeBundle\Entity;

use Erp\PaymentBundle\Entity\StripeCustomer;
use Erp\PaymentBundle\Entity\StripeAccount;
use Erp\UserBundle\Entity\User;
use PHY\CacheBundle\Cache;
use Stripe\Customer;
use Stripe\BankAccount;
use Stripe\Card;

class UserManager {

    /**
     * @var ApiManager
     */
    private $apiManager;

    /**
     * @var Cache
     */
    private $cache;

    public function __construct(ApiManager $apiManager, Cache $cache) {
        $this->apiManager = $apiManager;
        $this->cache = $cache;
    }

    /**
     * 
     * @param User $user
     * @return BankAccount
     */
    public function getBankAccount(User $user) {
        //TODO Connected account also have payment details
        /** @var StripeCustomer $stripeCustomer */
        $stripeCustomer = $user->getStripeCustomer(StripeCustomer::BANK_ACCOUNT);

        if (!$stripeCustomer) {
            return;
        }

        $arguments = [
            'id' => $stripeCustomer->getCustomerId(),
            'options' => null,
        ];

        if ($user->hasRole(User::ROLE_TENANT)) {
            $manager = $user->getTenantProperty()->getUser();
            $managerStripeAccountId = $manager->getStripeAccount()->getAccountId();
            $arguments['options'] = ['stripe_account' => $managerStripeAccountId];
        }

        $response = $this->apiManager->callStripeApi('\Stripe\Customer', 'retrieve', $arguments);

        if (!$response->isSuccess()) {
            return;
        }

        /** @var Customer $content */
        $content = $response->getContent();
        $sources = $content->sources;

        foreach ($sources->data as $source) {
            if ($source instanceof BankAccount) {
                return $source;
            }
        }

        return null;
    }

    /**
     * 
     * @param User $user
     * @return Card
     */
    public function getCreditCard(User $user) {
        /** @var StripeCustomer $stripeCustomer */
        $stripeCustomer = $user->getStripeCustomer(StripeCustomer::CREDIT_CARD);

        if (!$stripeCustomer) {
            return;
        }

        $arguments = [
            'id' => $stripeCustomer->getCustomerId(),
            'options' => null,
        ];

        if ($user->hasRole(User::ROLE_TENANT)) {
            $manager = $user->getTenantProperty()->getUser();
            $managerStripeAccountId = $manager->getStripeAccount()->getAccountId();
            $arguments['options'] = ['stripe_account' => $managerStripeAccountId];
        }

        $response = $this->apiManager->callStripeApi('\Stripe\Customer', 'retrieve', $arguments);

        if (!$response->isSuccess()) {
            return;
        }

        /** @var Customer $content */
        $content = $response->getContent();
        $sources = $content->sources;

        foreach ($sources->data as $source) {
            if ($source instanceof Card) {
                return $source;
            }
        }

        return null;
    }

    /**
     * 
     * @param User $user
     * @return type
     */
    public function getCustomerInfo(User $user) {
        //TODO Connected account details
        /** @var StripeCustomer $stripeCustomer */
        $stripeCustomer = $user->getStripeCustomer();

        if (!$stripeCustomer) {
            return;
        }

        $arguments = [
            'id' => $stripeCustomer->getCustomerId(),
            'options' => null,
        ];
        return $arguments;
    }

    public function getStripeAccountInfo(User $user) {
        //TODO Connected account details
        /** @var StripeCustomer $stripeCustomer */
        $StripeAccount = $user->getStripeAccount();

        if (!$StripeAccount) {
            return;
        }

        $arguments = [
            'id' => $StripeAccount->getAccountId(),
            'options' => null,
        ];
        return $arguments;
    }

}
