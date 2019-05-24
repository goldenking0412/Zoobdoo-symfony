<?php
namespace Erp\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DeploymentCommand
 */
class DeploymentCommand extends ContainerAwareCommand
{
    private $rootPath = '';

    /**
     * Set command name and description
     */
    protected function configure()
    {
        $this->setName('app:core:deployment')
            ->setDescription('Project deployment command');
    }

    /**
     * Execute command
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->rootPath = $this->getContainer()->get('kernel')->getRootdir();

        $this->generalUpdate();
        $output->writeln('Done');
    }

    /**
     * global commands for update
     * @return array
     */
    protected function generalUpdate()
    {
        $result = [];
        $result[] = shell_exec('php ' . $this->rootPath . '/console --no-interaction doctrine:migrations:migrate ');
        $result[] = shell_exec('php ' . $this->rootPath . '/console assets:install');
        $result[] = shell_exec('php ' . $this->rootPath . '/console cache:clear --env=prod');

        return $result;
    }
}
