<?php

namespace Erp\PropertyBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class PropertyExtension
 *
 * @package Erp\PropertyBundle\Twig
 */
class PropertyExtension extends \Twig_Extension {

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
        return 'property_extension';
    }

    /**
     * @return array
     */
    public function getFunctions() {
        return array(
            new \Twig_SimpleFunction('get_value_of_bath', array($this, 'getValueOfBath')),
            new \Twig_SimpleFunction('get_value_of_bed', array($this, 'getValueOfBed')),
        );
    }

    /**
     * Get value of bath by key
     *
     * @param $key
     *
     * @return null
     */
    public function getValueOfBath($key) {
        $baths = $this->container->get('erp.property.service')->getListOfBaths();

        return (isset($baths[$key])) ? $baths[$key] : null;
    }

    /**
     * Get value of beds by key
     *
     * @param $key
     *
     * @return null
     */
    public function getValueOfBed($key) {
        $beds = $this->container->get('erp.property.service')->getListOfBeds();

        return (isset($beds[$key])) ? $beds[$key] : null;
    }

}
