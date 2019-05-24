<?php

namespace Erp\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Erp\UserBundle\Entity\Charge;

class EmailnvoicesCommand extends ContainerAwareCommand {

    const COMMAND_NAME = 'erp:invoice:sendrecurrent';
    const COMMAND_DESC = 'Send recurring or unpaid invoices by email.';
    
    protected $daysRemind = 3;
    protected $paymentInterval = 30;

    /**
     * 
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output) {
        parent::initialize($input, $output);
    }

    /**
     * @inheritdoc
     */
    public function configure() {
        $this->setName(self::COMMAND_NAME)
                ->setDescription(self::COMMAND_DESC)
                ->setDefinition(new InputDefinition(array(
                    new InputOption('days-remind', 'r', InputOption::VALUE_OPTIONAL, 'Specify this option if you want to set days of reminding for unpaid invoices by yourself (default 3 days)', $this->daysRemind),
                    new InputOption('payment-interval', 'p', InputOption::VALUE_OPTIONAL, 'Specify this option if you want to set a different pyament interval for invoices (default 30 days)', $this->paymentInterval)

                )))
                ->setHelp(<<<EOT
The <info>%command.name%</info> automatically sends emails with the unpaid invoices, or with new invoices in case of recurring items.

    <info>php %command.full_name%</info>

The command automatically finds unpaid invoices and sent reminders for payments. Moreover
it emails new invoices in case of recurrent charges by manager.
                        
Unpaid charges are re-sent after 30 days, 60 days, and so forth.
Moreover, if a charge is recurrent, then it is "cloned" and sent by email on the day of recurrence.
The "cloning" is due to the fact that each payment should be made under an invoice, and an invoice
should be created for each request of payment, for fiscal purposes.
                        
The command takes into account the recurrent charges even considering that the months do not end
with the same day-number (e.g., last day of February is 28, unless for leap years).
In this way, the function checks if the next day of the actual date passed is equal to 1.
If it is, then all the charges with recurringDayOfMonth >= day(actual_date) are considered.
Otherwise, selection is carried out just on '=' comparison
EOT
        );
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $startedAt = microtime(true);
        $initialMemory = memory_get_usage();
        $output->writeln('<info>Executing command erp:invoice:sendrecurrent</info>');

        $container = $this->getContainer();

        $today = new \DateTime();

        $em = $container->get('doctrine')->getManagerForClass(Charge::class);
        $repository = $em->getRepository(Charge::class);

        // unpaid charges, sent after $daysRemind days, 2*$daysRemind days, and so forth
        $daysRemind = (is_int($input->getOption('days-remind'))) ? $input->getOption('days-remind') : $this->daysRemind;
        $paymentInterval = (is_int($input->getOption('payment-interval'))) ? $input->getOption('payment-interval') : $this->paymentInterval;

        $unpaidCharges = $repository->findUnpaidCharges($today, $paymentInterval, $daysRemind);
        foreach ($unpaidCharges as $charge) {
            if ($container->get('erp_user.mailer.processor')->sendChargeEmail($charge)) {
                $charge->setStatus(Charge::STATUS_SENT);
            }
        }

        // recurring charges
        $recurringCharges = $repository->findRecurringCharges($today);
        foreach ($recurringCharges as $charge) {
            $newCharge = clone $charge;
            
            $newCharge->setStatus(Charge::STATUS_NEW);
            $newCharge->setRecurringDayOfMonth(null);
            $newCharge->setParent($charge);
            $newCharge->getChildren()->clear();
            
            $charge->addChild($newCharge);

            $em->persist($newCharge);
            $em->flush();

            if ($this->get('erp_user.mailer.processor')->sendChargeEmail($newCharge)) {
                $newCharge->setStatus(Charge::STATUS_SENT);
                $em->flush();
            }
        }

        $em->flush();
        $em->clear();

        $output->writeln('<comment>COMPLETED... Done!</comment>');

        $endedAt = microtime(true);
        $finalMemory = memory_get_usage();
        $output->writeln(sprintf('<comment>Execution time: %d seconds</comment>', ($endedAt - $startedAt)));
        $output->writeln(sprintf('<comment>Execution memory: %d KB</comment>', ($finalMemory - $initialMemory) / 1024));
        $output->writeln('<info>Done.</info>');
    }

}
