<?php

namespace Erp\NotificationBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Erp\PropertyBundle\Entity\Property;
use Erp\NotificationBundle\Entity\UserNotification;
use Erp\NotificationBundle\Entity\History;
use Erp\NotificationBundle\Entity\Template;

class NotifyUsersBeforeRentDueDateCommand extends BaseNotificationCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('erp:notification:notify-users-before-rent-due-date')
            ->setDescription('Notify users before rent due date');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $out = $this->process(self::TYPE_NOTIFICATION);
        $output->writeln($out);
    }
}
