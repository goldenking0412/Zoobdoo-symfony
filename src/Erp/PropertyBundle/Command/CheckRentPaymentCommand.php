<?php

namespace Erp\PropertyBundle\Command;

use Erp\UserBundle\Entity\RentPaymentBalance;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Erp\PropertyBundle\Entity\Property;

class CheckRentPaymentCommand extends ContainerAwareCommand {

    /**
     * @inheritdoc
     */
    protected function configure() {
        $this
                ->setName('erp:property:check-rent-payment')
                ->setDescription('Check rent payment');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $repository = $em->getRepository(Property::class);
        $properties = $repository->getScheduledPropertiesForPayment();

        $i = 0;
        /** @var Property $property */
        foreach ($properties as $property) {
            $tenant = $property->getTenantUser();
            if (!$tenant) {
                continue;
            }

            // TODO Create Payment balance when tenant register
            if (!$rentPaymentBalance = $tenant->getRentPaymentBalance()) {
                $rentPaymentBalance = new RentPaymentBalance();
                $rentPaymentBalance = $rentPaymentBalance->setUser($tenant);
                $tenant->setRentPaymentBalance($rentPaymentBalance);
            }

            $propertySettings = $property->getSettings();
            $paymentAmount = $propertySettings->getPaymentAmount();

            $rentPaymentBalance->takeMoneyFromBalance($paymentAmount);
            $em->persist($tenant);

            if (( ++$i % 20) == 0) {
                $em->flush();
                $em->clear();
            }
        }

        $em->flush();
        $em->clear();
    }

}
