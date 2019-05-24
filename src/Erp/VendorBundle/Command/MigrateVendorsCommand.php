<?php

namespace Erp\VendorBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DeploymentCommand
 */
class MigrateVendorsCommand extends ContainerAwareCommand {

    const COMMAND_NAME = 'app:vendor:migrate';
    const COMMAND_DESC = 'Migrate vendors towards the new structures of entities.';

    /**
     * 
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output) {
        parent::initialize($input, $output);
    }

    /** @var OutputInterface */
    private $output;

    /** @var InputInterface */
    private $input;

    /** filesystem utility */
    private $fs;

    protected function configure() {
        $this->setName(self::COMMAND_NAME)
                ->setDescription(self::COMMAND_DESC)
                ->setHelp(<<<EOT
The <info>%command.name%</info> migrates the existing vendors towards the new structures of entities.

    <info>php %command.full_name%</info>
EOT
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        
        $this->removeAllVendorCreate($em);
        $this->removeAllVendorEdit($em);
        
        $vendors = $em->getRepository(\Erp\VendorBundle\Entity\Vendor::class)->findAll();
        
        foreach ($vendors as $vendor) {
            $vendorCreate = $this->getVendorCreateFromVendor($vendor);
            $vendorEdit = $this->getVendorEditFromVendor($vendor);
            
            $vendorCreate->addVendorEdit($vendorEdit);
            
            $em->persist($vendorCreate);
            $em->remove($vendor);
        }
        
        $em->flush();

        $output->writeln('<comment>All done.</comment>');
    }
    
    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $em
     */
    private function removeAllVendorCreate(\Doctrine\Common\Persistence\ObjectManager $em) {
        $vendorCreates = $em->getRepository(\Erp\VendorBundle\Entity\VendorCreate::class)->findAll();
        
        foreach ($vendorCreates as $item) {
            $em->remove($item);
        }
        
        $em->flush();
    }
    
    /**
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $em
     */
    private function removeAllVendorEdit(\Doctrine\Common\Persistence\ObjectManager $em) {
        $vendorEdits = $em->getRepository(\Erp\VendorBundle\Entity\VendorEdit::class)->findAll();
        
        foreach ($vendorEdits as $item) {
            $em->remove($item);
        }
        
        $em->flush();
    }
    
    /**
     * 
     * @param \Erp\VendorBundle\Entity\Vendor $vendor
     * @return \Erp\VendorBundle\Entity\VendorCreate
     */
    private function getVendorCreateFromVendor(\Erp\VendorBundle\Entity\Vendor $vendor) {
        $vendorCreate = new \Erp\VendorBundle\Entity\VendorCreate();
        
        $vendorCreate
                ->setEmail($vendor->getEmail())
                ->setName($vendor->getName())
        ;
        
        return $vendorCreate;
    }
    
    /**
     * 
     * @param \Erp\VendorBundle\Entity\Vendor $vendor
     * @return \Erp\VendorBundle\Entity\VendorCreate
     */
    private function getVendorEditFromVendor(\Erp\VendorBundle\Entity\Vendor $vendor) {
        $vendorEdit = new \Erp\VendorBundle\Entity\VendorEdit();
        
        $vendorEdit
                ->setAddress($vendor->getAddress())
                ->setBusinessType($vendor->getBusinessType())
                ->setCompanyName($vendor->getCompanyName())
                ->setContactEmail($vendor->getContactEmail())
                ->setContactPhone($vendor->getContactPhone())
                ->setFirstName($vendor->getFirstName())
                ->setLastName($vendor->getLastName())
                ->setWebsite($vendor->getWebsite())
        ;
        
        return $vendorEdit;
    }

}
