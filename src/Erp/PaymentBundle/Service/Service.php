<?php

namespace Erp\PaymentBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Erp\UserBundle\Entity\User;
use Erp\PaymentBundle\Entity\StripeCustomer;
use Erp\PropertyBundle\Entity\Property;
use Erp\StripeBundle\Entity\ApiManager;
use Erp\PaymentBundle\Entity\StripeDepositAccount;
use Erp\PaymentBundle\Entity\StripeAccount;
use Erp\PaymentBundle\Plaid\Service\Item as PlaidItem;
use Erp\PaymentBundle\Plaid\Service\Processor as PlaidProcessor;
use Erp\PaymentBundle\Plaid\Exception\ServiceException;
use Erp\PropertyBundle\Entity\ScheduledRentPayment;
use Erp\StripeBundle\Entity\Transaction;
use Erp\StripeBundle\Helper\ApiHelper;
use Erp\StripeBundle\Entity\CreditCard;
use Erp\UserBundle\Entity\Charge;
use Erp\PaymentBundle\Entity\StripeSubscription;

class Service {

    /** @var ObjectManager $om */
    protected $om;

    /** @var ApiManager $api */
    protected $api;

    /** @var PlaidItem $plaidItem */
    protected $plaidItem;

    /** @var PlaidProcessor $plaidProcessor */
    protected $plaidProcessor;

    /**
     * Constructor
     * 
     * @param ObjectManager $om
     * @param ApiManager $apiManager
     * @param PlaidItem $plaidItem
     * @param PlaidProcessor $plaidProcessor
     */
    public function __construct(ObjectManager $om, ApiManager $apiManager, PlaidItem $plaidItem, PlaidProcessor $plaidProcessor) {
        $this->om = $om;
        $this->api = $apiManager;
        $this->plaidItem = $plaidItem;
        $this->plaidProcessor = $plaidProcessor;
    }

    /**
     * 
     * @param User $user
     * @param string $stripeBankAccountToken
     * @param array|null $options
     * @return array
     */
    public function manageStripeCustomer(User &$user, $stripeBankAccountToken, $options) {
        $stripeCustomer = $user->getStripeCustomer(StripeCustomer::BANK_ACCOUNT);
        $notExistingStripeCustomer = (!($stripeCustomer));

        // manage Stripe Customer
        if (!$notExistingStripeCustomer) { // create a new Stripe Customer if doesn't exist
            $arguments = array(
                'params' => array(
                    'email' => $user->getEmail(),
                    'source' => $stripeBankAccountToken,
                ),
                'options' => $options,
            );
            $response = $this->api->callStripeApi('\Stripe\Customer', 'create', $arguments);
            if ($response->isSuccess()) {
                $stripeCustomer = $this->flushStripeObjects($response, $user);
            }
        } else { // update the existing Stripe Customer
            $arguments = array(
                'id' => $stripeCustomer->getCustomerId(),
                'params' => array('source' => $stripeBankAccountToken),
                'options' => $options,
            );
            $response = $this->api->callStripeApi('\Stripe\Customer', 'update', $arguments);
        }

        return array($response, $notExistingStripeCustomer);
    }

    /**
     * 
     * @param User $user
     * @param string $stripeBankAccountToken
     * @param string $country
     * @param string $type
     * @param array|null $options
     * @param string|null $clientIp
     * @param Property|null $property
     * @return mixed
     * @throws \Exception
     */
    public function manageStripeAccount(User &$user, $stripeBankAccountToken, $country, $type, $options, $clientIp = null, Property &$property = null) {
        $stripeAccount = (is_null($property))
                ? $user->getStripeAccount()
                : $property->getDepositAccount()
        ;
        $notExistingStripeAccount = (!($stripeAccount) || !($stripeAccount->getAccountId()));

        if ($notExistingStripeAccount) { // create a new Stripe Account
            if ($property) {
                $stripeAccount = $this->flushStripeObjectForDeposit($user, $property, true, $clientIp);
            }
            
            $params = array_merge($stripeAccount->toStripe(), array(
                'country' => $country,
                'type' => $type,
                'external_account' => $stripeBankAccountToken,
            ));
            $arguments = array(
                'params' => $params,
                'options' => null,
            );
            $response = $this->api->callStripeApi('\Stripe\Account', 'create', $arguments);
            if ($response->isSuccess()) {
                if (!($property)) {
                    $this->flushStripeObjects($response, $user, true);
                } else {
                    $stripeAccount = $this->flushStripeObjectForDeposit($user, $property, true, null, $response);
                }
            }
        } else { // update the Stripe Account as well as the Stripe Customer
            $arguments = array(
                'id' => $stripeAccount->getAccountId(),
                'params' => array('external_account' => $stripeBankAccountToken)
            );
            $response = $this->api->callStripeApi('\Stripe\Account', 'update', $arguments);
            if ($response->isSuccess()) {
                $arguments = array(
                    'id' => $user->getStripeCustomer(StripeCustomer::BANK_ACCOUNT)->getCustomerId(),
                    'params' => array('source' => $stripeBankAccountToken),
                    'options' => $options,
                );
                $response = $this->api->callStripeApi('\Stripe\Customer', 'update', $arguments);
            }
        }
        
        return $response;
    }
    
    /**
     * 
     * @param StripeAccount $stripeAccount
     * @param string $clientIp
     * @return Response
     */
    public function verifyAccount(StripeAccount $stripeAccount, $clientIp) {
        $stripeAccount->setTosAcceptanceDate(new \DateTime())
                ->setTosAcceptanceIp($clientIp);

        $arguments = [
            'id' => $stripeAccount->getAccountId(),
            'params' => $stripeAccount->toStripe(),
            'options' => null,
        ];
        return $this->api->callStripeApi('\Stripe\Account', 'update', $arguments);
    }

    /**
     * 
     * @param string $publicToken
     * @param string $accountId
     * @return string
     * @throws ServiceException
     */
    public function createBankAccountToken($publicToken, $accountId) {
        $response = $this->plaidItem->exchangePublicToken($publicToken);
        $result = json_decode($response['body'], true);

        if (($response['code'] < 200) || ($response['code'] >= 300)) {
            throw new ServiceException($result['display_message']);
        }

        $response = $this->plaidProcessor->createBankAccountToken($result['access_token'], $accountId);
        $result = json_decode($response['body'], true);

        if (($response['code'] < 200) || ($response['code'] >= 300)) {
            throw new ServiceException($result['display_message']);
        }

        return $result['stripe_bank_account_token'];
    }
    
    /**
     * 
     * @param StripeAccount $account
     * @param type $remoteAccount
     * @param type $stripeBankAccountToken
     * @return Response
     */
    public function updateBankAccount(StripeAccount $account, $remoteAccount, $stripeBankAccountToken) {
        if (!$account->getBankAccountId()) {
            $bankAccount = $remoteAccount->external_accounts->create(array('external_account' => $stripeBankAccountToken));
            
            $account
                    ->setBankAccountId($bankAccount['id'])
                    ->setBankName($bankAccount['bank_name'])
                    ->setAccountHolderName($bankAccount['account_holder_name'])
                    ->setRoutingNumber($bankAccount['routing_number'])
            ;
            
            $this->em->persist($account);
            $this->em->flush();
        } else {
            /* $arguments = array(
                'id' => $account->getBankAccountId(),
                'customer' => $remoteCustomer
            ); */
            $bankAccount = $account->external_accounts->retrieve($account->getBankAccountId());
        }
        
        return $bankAccount;
    }
    
    /**
     * 
     * @param mixed $object
     * @param integer $amount
     * @param array $metadata
     * @param StripeAccount|null $stripeAccount
     * @param mixed|null $stripeCustomer
     * @param integer|null $bankAccountId
     * @return Response|null
     */
    public function makeSinglePayment($object, $amount, $metadata = array(), StripeAccount $stripeAccount = null, $stripeCustomer = null, $bankAccountId = null) {
        if (is_null($object)) {
            $arguments = array(
                'params' => array(
                    'amount' => ApiHelper::convertAmountToStripeFormat($amount),
                    'customer' => ($stripeCustomer instanceof StripeCustomer) ? $stripeCustomer->getCustomerId() : $stripeCustomer,
                    'currency' => StripeCustomer::DEFAULT_CURRENCY,
                ),
                'options' => null,
            );
            
            if (!(is_null($stripeAccount))) {
                $arguments['destination'] = array(
                    'amount' => ApiHelper::convertAmountToStripeFormat($amount),
                    'account' => $stripeAccount->getAccountId(),
                );
                $arguments['source'] = $bankAccountId;
                $arguments['capture'] = 'true';
                $arguments['options'] = array('destination' => $stripeAccount->getAccountId());
            }
        } else {
            switch (get_class($object)) {
                case (ScheduledRentPayment::class):
                    $customer = is_null($stripeCustomer)
                            ? $object->getCustomer()
                            : $stripeCustomer
                    ;
                    $account = is_null($stripeAccount)
                            ? $object->getAccount()
                            : $stripeAccount
                    ;

                    $arguments = array(
                        'params' => array(
                            //TODO Refactoring amount in payRentAction form
                            'amount' => ApiHelper::convertAmountToStripeFormat($amount),
                            'currency' => StripeCustomer::DEFAULT_CURRENCY,
                            'customer' => $customer->getCustomerId(),
                            'metadata' => $metadata,
                        ),
                        'options' => array(
                            'stripe_account' => $account->getAccountId()
                        )
                    );
                    break;
                case (CreditCard::class):
                    $arguments = array(
                        'params' => array(
                            'amount' => ApiHelper::convertAmountToStripeFormat($amount),
                            'source' => $object->getSourceToken(),
                            'currency' => StripeCustomer::DEFAULT_CURRENCY,
                        ),
                        'options' => array(
                            'stripe_account' => $stripeAccount->getAccountId(),
                        )
                    );

                    break;
                case (StripeCustomer::class):
                    $arguments = array(
                        'params' => array(
                            //TODO Refactoring amount in payRentAction form
                            'amount' => ApiHelper::convertAmountToStripeFormat($amount),
                            'currency' => StripeCustomer::DEFAULT_CURRENCY,
                            'customer' => $object->getCustomerId(),
                            'metadata' => array(
                                'account' => $stripeCustomer->getCustomerId(),
                                'internalType' => 'deposit_refund'
                            ),
                        ),
                        'options' => array(
                            'stripe_account' => $stripeAccount->getAccountId()
                        )
                    );
                    break;
                case (Charge::class):
                    $arguments = array(
                        'params' => array(
                            'amount' => ApiHelper::convertAmountToStripeFormat($amount),
                            'customer' => $object->getReceiver()->getStripeCustomer()->getCustomerId(),
                            'currency' => StripeCustomer::DEFAULT_CURRENCY,
                            'metadata' => $metadata,
                        ),
                        'options' => array(
                            'stripe_account' => $stripeAccount->getAccountId(),
                        )
                    );
                    break;
                default:
                    $arguments = array();
                    break;
            }
        }
        
        return (!(empty($arguments))) ? null : $this->api->callStripeApi('\Stripe\Charge', 'create', $arguments);
    }
    
    /**
     * 
     * @param StripeCustomer $stripeCustomer
     * @param Transaction $transaction
     * @return Response
     */
    public function refund(StripeCustomer $stripeCustomer, Transaction $transaction) {
        $stripeAccountId = $stripeCustomer->getAccountId();

        $arguments = array(
            'params' => array(
                'amount' => ApiHelper::convertAmountToStripeFormat($transaction->getAmount()),
                'charge' => $transaction->getStripeId(),
                'metadata' => array(
                    'account' => $stripeAccountId,
                    'internalType' => Transaction::INTERNAL_TYPE_REFUND
                )),
            'options' => array(
                'stripe_account' => $stripeAccountId,
            )
        );

        return $this->api->callStripeApi('\Stripe\Refund', 'create', $arguments);
    }
    
    /**
     * @param Charge $charge
     * @param mixed $sourceToken
     * @param string $stripeAccountId
     * @return Response
     */
    public function createStripeCustomer(Charge $charge, $sourceToken, $stripeAccountId) {
        $payer = $charge->getReceiver();

        $response = $this->api->callStripeApi('\Stripe\Customer', 'create', array(
            'params' => array(
                'email' => $payer->getEmail(),
                'source' => $sourceToken,
            ),
            'options' => array('stripe_account' => $stripeAccountId)
        ));
        
        if ($response->isSuccess()) {
            /** @var \Stripe\Customer $externalStripeCustomer */
            $externalStripeCustomer = $response->getContent();

            $receiverStripeCustomer = new StripeCustomer();
            $receiverStripeCustomer->setCustomerId($externalStripeCustomer->id)->setUser($payer);

            $this->om->persist($payer);
            $this->om->flush();
        } else {
            $this->setChargeAsPending($charge);
        }
        
        return $response;
    }
    
    /**
     * 
     * @param Charge $charge
     * @param string $stripeAccountId
     * @return Response
     */
    public function retrieveStripePlan(Charge $charge, $stripeAccountId) {
        $arguments = [
            'id' => StripeSubscription::MONTHLY_PLAN_ID,
            'options' => [
                'stripe_account' => $stripeAccountId,
            ]
        ];
        $response = $this->api->callStripeApi('\Stripe\Plan', 'retrieve', $arguments);
        
        if ($response->isSuccess()) {
            $response = $this->createStripePlan($stripeAccountId);
        } else {
            $this->setChargeAsPending($charge);
        }
        
        return $response;
    }
    
    /**
     * 
     * @param Charge $charge
     * @param string $stripeAccountId
     * @return Response
     */
    public function createStripeSubscription(Charge $charge, $stripeAccountId) {
        $payer = $charge->getReceiver()->getStripeCustomer();

        $arguments = array(
            'params' => array(
                'customer' => $payer->getCustomerId(),
                'items' => array(
                    array(
                        'plan' => StripeSubscription::MONTHLY_PLAN_ID,
                        'quantity' => ApiHelper::convertAmountToStripeFormat($charge->getAmount()),
                    ),
                ),
            ),
            'options' => array(
                'stripe_account' => $stripeAccountId,
            )
        );
        $response = $this->api->callStripeApi('\Stripe\Subscription', 'create', $arguments);

        if ($response->isSuccess()) {
            /** @var Subscription $subscription */
            $subscription = $response->getContent();

            $stripeSubscription = new StripeSubscription();
            $stripeSubscription->setSubscriptionId($subscription['id'])->setStripeCustomer($payer);

            $this->om->persist($payer);
            $this->om->flush();
        } else {
            $this->setChargeAsPending($charge);
        }

        return $response;
    }
    
    /**
     * 
     * @param Charge $charge
     * @return Response
     */
    public function updateStripeSubscription(Charge $charge) {
        $payerSubscription = $charge->getReceiver()->getStripeCustomer()->getStripeSubscription();

        //TODO ERP-191
        $arguments = array(
            'id' => $payerSubscription->getSubscriptionId(),
            'params' => array(
                'quantity' => $charge->getAmount(),
            ),
            'options' => null,
        );
        $response = $this->api->callStripeApi('\Stripe\Subscription', 'update', $arguments);

        if (!$response->isSuccess()) {
            $this->setChargeAsPending($charge);
        }
        
        return $response;
    }
    
    /**
     * 
     * @param User $user
     * @param float $amount
     * @param float $newAmount
     * @return Response
     */
    public function buyUnit(User $user, $amount, $newAmount) {
        $stripeCustomer = $user->getStripeCustomer();
        
        if (!$stripeSubscription = $stripeCustomer->getStripeSubscription()) { // create
            $arguments = array(
                'params' => array(
                    'customer' => $stripeCustomer->getCustomerId(),
                    'items' => array(
                        array(
                            'plan' => StripeSubscription::YEARLY_PLAN_ID,
                            'quantity' => $newAmount,
                        ),
                    ),
                    'trial_period_days' => StripeCustomer::TRIAL_PERIOD_DAYS,
                    'metadata' => array(
                        'internalType' => Transaction::INTERNAL_TYPE_ANNUAL_SERVICE_FEE
                    ),
                ),
                'options' => null,
            );
            
            $response = $this->api->callStripeApi('\Stripe\Subscription', 'create', $arguments);
            if ($response->isSuccess()) {
                /** @var Subscription $subscription */
                $subscription = $response->getContent();

                $stripeSubscription = new StripeSubscription();
                $stripeSubscription->setSubscriptionId($subscription['id'])
                        ->setStripeCustomer($stripeCustomer)
                        ->setTrialPeriodStartAt(new \DateTime());

                $this->om->persist($stripeSubscription);
                $this->om->flush();
            }
        } else { // update
            $arguments = array(
                'id' => $stripeSubscription->getSubscriptionId(),
                'params' => array(
                    'quantity' => $newAmount,
                ),
                'options' => null,
            );
            
            $response = $this->api->callStripeApi('\Stripe\Subscription', 'update', $arguments);
            if ($response->isSuccess()) {
                $amount = $newAmount - $amount;
                $response = $this->makeSinglePayment(null, $amount, array(), null, $stripeCustomer);
            }
            
            return $response;
        }
    }
    
    /**
     * 
     * @param Charge $charge
     */
    public function setChargeAsPending(Charge $charge) {
        $charge->setStatus(Charge::STATUS_PENDING);
        $this->om->persist($charge);
        $this->om->flush();
    }
    
    /**
     * @param string $stripeAccountId
     * @return boolean
     */
    private function createStripePlan($stripeAccountId) {
        $arguments = array(
            'params' => array(
                'amount' => 1,
                'interval' => 'month',
                "currency" => 'usd',
                'name' => StripeSubscription::MONTHLY_PLAN_ID,
                'id' => StripeSubscription::MONTHLY_PLAN_ID,
            ),
            'options' => array(
                'stripe_account' => $stripeAccountId,
            )
        );
        $response = $this->api->callStripeApi('\Stripe\Plan', 'create', $arguments);
        
        return $response;
    }

    /**
     * 
     * @param mixed $response
     * @param User $user
     * @param boolean $forManagers (default false)
     * @return StripeCustomer | StripeAccount
     */
    private function flushStripeObjects($response, User $user, $forManagers = false) {
        if ($forManagers) {
            /** @var Account $account */
            $account = $response->getContent();

            $object = $user->getStripeAccount();
            $object->setAccountId($account['id']);
        } else {
            /** @var Customer $customer */
            $customer = $response->getContent();

            $object = new StripeCustomer();
            $object
                    ->setCustomerId($customer['id'])
                    ->setUser($user)
                    ->setType(StripeCustomer::BANK_ACCOUNT)
            ;
        }

        $this->om->persist($object);
        $this->om->flush();

        return $object;
    }

    /**
     * 
     * @param User $user
     * @param Property $property
     * @param boolean $forManagers (default false)
     * @param string|null $clientIp
     * @param mixed $response
     * @return StripeCustomer | StripeAccount
     */
    private function flushStripeObjectForDeposit(User $user, Property $property, $forManagers = false, $clientIp = null, $response = null) {
        if ($forManagers) {
            if (is_null($response) || !($property->getDepositAccount()))  {
                $object = new StripeDepositAccount();
                $object
                        ->setTosAcceptanceDate(new \DateTime())
                        ->setTosAcceptanceIp($clientIp)
                ;
                // $stripeAccount->setUser($user);
                $property->setDepositAccount($object);
                $this->om->persist($property);
            } else {
                /** @var Account $account */
                $account = $response->getContent();
                $object = $property->getDepositAccount();
                $object->setAccountId($account['id']);
            }
        } else {
            /** @var Customer $customer */
            $customer = $response->getContent();

            $object = new StripeCustomer();
            $object
                    ->setCustomerId($customer['id'])
                    ->setUser($user)
                    ->setType(StripeCustomer::BANK_ACCOUNT)
            ;
        }

        $this->om->persist($object);
        $this->om->flush();

        return $object;
    }

}
