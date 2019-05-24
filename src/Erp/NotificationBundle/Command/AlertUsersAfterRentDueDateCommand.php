<?php

namespace Erp\NotificationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Erp\PropertyBundle\Entity\Property;
use Erp\NotificationBundle\Entity\UserNotification;
use Erp\NotificationBundle\Entity\History;
use Erp\NotificationBundle\Entity\Template;

class AlertUsersAfterRentDueDateCommand extends BaseNotificationCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('erp:notification:alert-users-after-rent-due-date')
            ->setDescription('Alert users after rent due date');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $out = $this->process(self::TYPE_ALERT);
        $output->writeln($out);
    }
}
