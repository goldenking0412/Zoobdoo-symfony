<?php

namespace Erp\PropertyBundle\Command;

use Erp\StripeBundle\Entity\Transaction;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Erp\PropertyBundle\Entity\ScheduledRentPayment;
use Doctrine\Common\Persistence\ObjectManager;

class CheckScheduledPaymentCommand extends ContainerAwareCommand {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @inheritdoc
     */
    protected function configure() {
        $this
                ->setName('erp:property:check-scheduled-payment')
                ->setDescription('Charge Tenants');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        /** @var Doctrine\Common\Persistence\ObjectManager $em */
        $em = $this->getContainer()->get('doctrine')->getManagerForClass(ScheduledRentPayment::class);
        
        $recurringPaymentRepository = $em->getRepository(ScheduledRentPayment::class);

        $scheduledRentPayments = $recurringPaymentRepository->getScheduledRecurringPayments();
        $scheduledSinglePayments = $recurringPaymentRepository->getScheduledSinglePayments();

        $this->makePayment($em, $scheduledRentPayments);
        $this->makePayment($em, $scheduledSinglePayments);
    }

    /**
     * 
     * @param array $payments
     */
    private function makePayment(ObjectManager $em, array $payments) {
        $container = $this->getContainer();
        $logger = $container->get('logger');
        
        $i = 0;
        /** @var ScheduledRentPayment $payment */
        foreach ($payments as $payment) {
            $property = $payment->getProperty();

            $metadataInternalStatus = ($payment->getStatus() == ScheduledRentPayment::STATUS_FAILURE)
                    ? Transaction::INTERNAL_TYPE_LATE_RENT_PAYMENT
                    : Transaction::INTERNAL_TYPE_RENT_PAYMENT
            ;
            
            $metadata = array(
                'account' => $payment->getAccount()->getAccountId(),
                'internalType' => $metadataInternalStatus,
                'propertyId' => $property->getId()
            );

            $response = $container->get('erp.payment.service')->makeSinglePayment($payment, $payment->getAmount(), $metadata);

            if (!$response->isSuccess()) {
                $status = ScheduledRentPayment::STATUS_FAILURE;
                $logger->critical(json_encode($response->getErrorMessage()));
            } else {
                $status = ScheduledRentPayment::STATUS_SUCCESS;
            }

            $payment->setStatus($status);

            if ($payment->isRecurring()) {
                $nextPaymentAt = $payment->getNextPaymentAt();
                $status === ScheduledRentPayment::STATUS_FAILURE ?
                                $nextPaymentAt->modify('+1 day') :
                                $nextPaymentAt->modify('+1 month');
            }

            $em->persist($payment);

            if (( ++$i % 20) == 0) {
                $em->flush();
                $em->clear();
            }
        }

        $em->flush();
        $em->clear();
    }

}
