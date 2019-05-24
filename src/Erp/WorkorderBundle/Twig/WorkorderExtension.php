<?php

namespace Erp\WorkorderBundle\Twig;

use Erp\WorkorderBundle\Entity\Workorder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Erp\UserBundle\Entity\User;

/**
 * Class WorkorderExtension
 *
 * @package Erp\WorkorderBundle\Twig
 */
class WorkorderExtension extends \Twig_Extension {

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getManager();
    }

    /**
     * @return string
     */
    public function getName() {
        return 'workorder_extension';
    }

    /**
     * @return array
     */
    public function getFunctions() {
        return array(
            new \Twig_SimpleFunction('count_uncompleted_workorders', array($this, 'getCountUncompletedWorkorders')),
        );
    }
    
    /**
     * 
     * @param User $user
     * @return integer
     */
    public function getCountUncompletedWorkorders(User $user) {
        return $this->em->getRepository(Workorder::REPOSITORY)->findCountUncompletedWorkOrdersByManager($user);
    }

}
