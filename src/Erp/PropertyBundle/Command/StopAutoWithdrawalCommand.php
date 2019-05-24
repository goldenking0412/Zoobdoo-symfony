<?php

namespace Erp\PropertyBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Erp\PropertyBundle\Entity\ScheduledRentPayment;

class StopAutoWithdrawalCommand extends ContainerAwareCommand
{
    /**
     * @inheritdoc
     */
    public function configure()
    {
        $this
            ->setName('erp:property:stop-auto-withdraw')
            ->setDescription('Charge Tenants');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManagerForClass(ScheduledRentPayment::class);
        $repository = $em->getRepository(ScheduledRentPayment::class);
        $scheduledRentPayments = $repository->getEndingScheduledRentPayments();

        $i = 0;
        foreach ($scheduledRentPayments as $scheduledRentPayment) {
            $em->remove($scheduledRentPayment);

            if ((++$i % 20) == 0) {
                $em->flush();
                $em->clear();
            }
        }

        $em->flush();
        $em->clear();
    }
}