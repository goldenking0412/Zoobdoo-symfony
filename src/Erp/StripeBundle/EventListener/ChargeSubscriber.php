<?php

namespace Erp\StripeBundle\EventListener;

use Erp\PropertyBundle\Entity\Property;
use Erp\StripeBundle\Entity\BalanceHistory;
use Erp\StripeBundle\Entity\Transaction;
use Erp\StripeBundle\Event\ChargeEvent;
use Erp\PaymentBundle\Entity\StripeAccount;
use Erp\PaymentBundle\Entity\StripeCustomer;
use Stripe\Charge as StripeCharge;
use Erp\UserBundle\Entity\Charge;

class ChargeSubscriber extends AbstractSubscriber {

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents() {
        return [
            ChargeEvent::SUCCEEDED => 'onChargeSucceeded',
            ChargeEvent::PENDING => 'onChargePending',
            ChargeEvent::UPDATED => 'onChargeUpdated',
            ChargeEvent::REFUNDED => 'onChargeRefunded'
        ];
    }

    /**
     * 
     * @param ChargeEvent $event
     */
    public function onChargeUpdated(ChargeEvent $event) {
        $this->onChargeSucceeded($event);
    }

    /**
     * 
     * @param ChargeEvent $event
     */
    public function onChargePending(ChargeEvent $event) {
        $this->onChargeSucceeded($event);
    }

    /**
     * 
     * @param ChargeEvent $event
     * @return type
     * @throws \InvalidArgumentException
     */
    public function onChargeSucceeded(ChargeEvent $event) {
        $stripeEvent = $event->getStripeEvent();
        $stripeCharge = $stripeEvent->data->object;

        /** @var Charge $stripeCharge */
        if (!$stripeCharge instanceof StripeCharge) {
            throw new \InvalidArgumentException('ChargeSubscriber::onChargeSucceeded() accepts only Stripe\Charge objects as second parameter.');
        }

        if (!$stripeCharge->customer) {
            return;
        }
        
        $this->buildAndSaveTransaction($stripeCharge);
    }

    /**
     * 
     * @param ChargeEvent $event
     * @return type
     * @throws \InvalidArgumentException
     */
    public function onChargeRefunded(ChargeEvent $event) {
        //TODO: current version change status, but we need to change it for create entities Refunds

        $stripeEvent = $event->getStripeEvent();
        $stripeCharge = $stripeEvent->data->object;

        /** @var Charge $stripeCharge */
        if (!$stripeCharge instanceof StripeCharge) {
            throw new \InvalidArgumentException('ChargeSubscriber::onChargeRefunded() accepts only Stripe\Charge objects as second parameter.');
        }

        if (!$stripeCharge->customer) {
            return;
        }
        
        $this->buildAndSaveTransaction($stripeCharge);
    }

    /**
     * 
     * @param string $accountId
     * @return StripeAccount
     */
    private function getAccount($accountId) {
        $em = $this->registry->getManagerForClass(StripeAccount::class);

        return $em->getRepository(StripeAccount::class)->findOneBy(['accountId' => $accountId]);
    }

    /**
     * 
     * @param string $customerId
     * @return StripeCustomer
     */
    private function getCustomer($customerId) {
        $em = $this->registry->getManagerForClass(StripeCustomer::class);

        return $em->getRepository(StripeCustomer::class)->findOneBy(['customerId' => $customerId]);
    }
    
    /**
     * 
     * @param StripeCharge $stripeCharge
     */
    private function buildAndSaveTransaction(StripeCharge $stripeCharge) {
        $em = $this->registry->getManagerForClass(Transaction::class);
        $repository = $em->getRepository(Transaction::class);
        $chargeRepository = $em->getRepository(Charge::class);
        $propertyRepository = $em->getRepository(Property::class);

        $stripeAccount = $this->getAccount($stripeCharge->metadata->account);
        $stripeAccountId = ($stripeAccount instanceof StripeAccount) ? $stripeAccount->getId() : null;
        $stripeCustomer = $this->getCustomer($stripeCharge->customer);
        $internalType = $stripeCharge->metadata->internalType;
        $internalChargeId = $stripeCharge->metadata->internalChargeId ? $stripeCharge->metadata->internalChargeId : null; //if exist
        $propertyId = $stripeCharge->metadata->propertyId ? $stripeCharge->metadata->propertyId : null; //if exist
        //get current balance based on
        /* @var $previousTransaction Transaction */

        $previousTransaction = $repository->findOneBy(['account' => $stripeAccountId], ['created' => 'DESC']);

        if ($previousTransaction instanceof Transaction && isset($stripeCharge->amount)) {
            if ($previousTransaction->getBalanceHistory() instanceof BalanceHistory) {
                $previousBalance = $previousTransaction->getBalanceHistory()->getBalance();
            } else {
                $previousBalance = 0;
            }
            $balance = $stripeCharge->amount + $previousBalance;
        } else {
            //first balance
            $balance = $stripeCharge->amount;
        }

        $charge = $chargeRepository->find($internalChargeId);
        $transaction = $repository->findOneBy(['account' => $stripeAccountId, 'amount' => $stripeCharge->amount, 'created' => (new \DateTime())->setTimestamp($stripeCharge->created)]);
        $property = $propertyRepository->find($propertyId);

        if ($transaction instanceof Transaction) {
            //exist transaction
            $balanceHistory = $transaction->getBalanceHistory();
        } else {
            //new transaction
            $transaction = new Transaction();
            $transaction
                    ->setBalance($balance)
                    ->setType(Transaction::TYPE_CHARGE)
                    ->setCurrency($stripeCharge->currency)
                    ->setCreated((new \DateTime())->setTimestamp($stripeCharge->created))
                    ->setAmount($stripeCharge->amount)
                    ->setPaymentMethod($stripeCharge->source->object)
                    ->setPaymentMethodDescription($stripeCharge->source->brand)
                    ->setInternalType($internalType)
                    ->setMetadata($stripeCharge->metadata->jsonSerialize())
                    ->setStripeId($stripeCharge->id)
            ;

            $balanceHistory = new BalanceHistory();
            $balanceHistory->setAmount($stripeCharge->amount);
            $balanceHistory->setBalance($balance);
        }

        //update for all cases
        $transaction->setProperty($property); //set if exist
        $transaction->setStatus($stripeCharge->status);
        $transaction->setAccount($stripeAccount);
        $transaction->setCustomer($stripeCustomer);
        $transaction->setCharge($charge); //set if exists
        $em->persist($transaction);
        $em->flush();

        $balanceHistory->setTransaction($transaction);
        $em->persist($balanceHistory);
        $em->flush();
    }

}
