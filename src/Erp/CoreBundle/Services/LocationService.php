<?php
namespace Erp\CoreBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;

class LocationService
{
    /**
     * @var EntityManager
     */
    protected $container;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var array
     */
    protected $states = [];

    /**
     * @var array
     */
    protected $cities = [];

    /**
     * Initialize method
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getManager();
        $this->setStates();
    }

    /**
     * Set states
     *
     * @param array $states
     *
     * @return $this
     */
    public function setStates(array $states = [])
    {
        if ($states) {
            $this->states = $states;
        }

        if (!$this->states) {
            $states = $this->em->getRepository('ErpCoreBundle:City')->getStatesQb()->getQuery()->getArrayResult();

            $result = [];
            foreach ($states as $state) {
                $result[$state['stateCode']] = $state['stateCode'];
            }
            $this->states = $result;
        }

        return $this;
    }

    /**
     * Get USA states
     *
     * @return array
     */
    public function getStates()
    {
        return $this->states;
    }

    /**
     * Set cities
     *
     * @param null|string $stateCode
     *
     * @return $this
     */
    public function setCities($stateCode = null)
    {
        $cities = $this->em->getRepository('ErpCoreBundle:City')
            ->getCitiesByStateCodeQb($stateCode)
            ->getQuery()
            ->getArrayResult();

        $result = [];
        foreach ($cities as $city) {
            $result[$city['id']] = $city['name'];
        }

        $this->cities = $result;

        return $this;
    }

    /**
     * Get cities
     *
     * @return array
     */
    public function getCities()
    {
        return $this->cities;
    }
}
