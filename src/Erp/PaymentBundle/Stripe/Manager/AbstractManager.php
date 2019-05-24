<?php

namespace Erp\PaymentBundle\Stripe\Manager;

use Erp\PaymentBundle\Stripe\Client\Client;
use Doctrine\Common\Persistence\ObjectManager;

abstract class AbstractManager {

    /** @var ObjectManager $om */
    protected $om;

    /**
     * @var Client
     */
    protected $client;

    /**
     * Constructor
     * 
     * @param Client $client
     * @param ObjectManager $om
     */
    public function __construct(Client $client, ObjectManager $om) {
        $this->client = $client;
        $this->om = $om;
    }

}
