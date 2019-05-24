<?php

namespace Erp\PaymentBundle\Stripe\Manager;

use Stripe\Customer;
use Stripe\BankAccount;
use Erp\PaymentBundle\Entity\StripeCustomer;
use Erp\UserBundle\Entity\User;

class CustomerManager extends AbstractManager {

    /**
     * 
     * @param User $user
     * @param array $params
     * @param array $options
     * @return type
     */
    public function create(User $user, $params, $options = null) {
        $response  = $this->client->sendCustomerRequest('create', $params, $options);
        
        if ($response->isSuccess()) {
            /** @var Customer $customer */
            $customer = $response->getContent();

            $stripeCustomer = new StripeCustomer();
            $stripeCustomer
                    ->setCustomerId($customer['id'])
                    ->setUser($user)
                    ->setType(StripeCustomer::CREDIT_CARD)
            ;

            $this->om->persist($stripeCustomer);
            $this->em->flush();
        }
        
        return $response;
    }

    /**
     * 
     * @param type $id
     * @param type $options
     * @return type
     */
    public function retrieve($id, $options = null) {
        return $this->client->sendCustomerRequest('retrieve', $id, $options);
    }

    /**
     * 
     * @param Customer $customer
     * @param type $params
     * @param type $options
     * @return type
     */
    public function createBankAccount(Customer $customer, $params, $options = null) {
        $params = array_merge($params, ['object' => 'bank_account']);

        return $this->client->sendCustomerSourceRequest($customer, 'create', ['source' => $params], $options);
    }

    /**
     * 
     * @param Customer $customer
     * @param type $id
     * @param type $options
     * @return type
     */
    public function retrieveBankAccount(Customer $customer, $id, $options = null) {
        return $this->client->sendCustomerSourceRequest($customer, 'retrieve', $id, $options);
    }

    /**
     * 
     * @param Customer $customer
     * @param type $id
     * @param type $options
     * @return type
     */
    public function retrieveCreditCard(Customer $customer, $id, $options = null) {
        return $this->client->sendCustomerSourceRequest($customer, 'retrieve', $id, $options);
    }

    /**
     * 
     * @param Customer $account
     * @param type $params
     * @param type $options
     * @return type
     */
    public function update(Customer $account, $params, $options = null) {
        return $this->client->sendUpdateRequest($account, $params, $options);
    }

}
