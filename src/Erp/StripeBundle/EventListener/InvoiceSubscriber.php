<?php

namespace Erp\StripeBundle\EventListener;

use Erp\StripeBundle\Event\InvoiceEvent;
use Erp\StripeBundle\Entity\Invoice;
use Erp\PaymentBundle\Entity\StripeAccount;
use Erp\PaymentBundle\Entity\StripeCustomer;

class InvoiceSubscriber extends AbstractSubscriber {

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents() {
        return [
            InvoiceEvent::CREATED => 'onChargeCreated',
        ];
    }

    /**
     * 
     * @param InvoiceEvent $event
     * @throws \InvalidArgumentException
     */
    public function onChargeCreated(InvoiceEvent $event) {
        $stripeEvent = $event->getStripeEvent();
        $stripeInvoice = $stripeEvent->data->object;

        /** @var \Stripe\Invoice $stripeEvent */
        if (!$stripeInvoice instanceof \Stripe\Invoice) {
            throw new \InvalidArgumentException('InvoiceSubscriber::onChargeCreated() accepts only Stripe\Charge objects as second parameter.');
        }

        $invoice = new Invoice();
        $invoice->setAmount($stripeInvoice->amount_due)
                ->setCreated((new \DateTime())->setTimestamp($stripeInvoice->date));

        if (isset($stripeEvent->account)) {
            $account = $this->getAccount($stripeEvent->account);
            $invoice->setAccount($account);
        }

        $customer = $this->getCustomer($stripeInvoice->customer);
        $invoice->setCustomer($customer);

        $em = $this->registry->getManagerForClass(Invoice::class);

        $em->persist($invoice);
        $em->flush();
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

}
