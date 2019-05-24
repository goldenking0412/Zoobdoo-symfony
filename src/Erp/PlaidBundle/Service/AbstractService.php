<?php

namespace Erp\PlaidBundle\Plaid\Service;

use Erp\CoreBundle\Services\Curl;

abstract class AbstractService
{
    /**
     * @var Curl
     */
    protected $curl;

    /**
     * @var string
     */
    protected $env;

    /**
     * @var string
     */
    private $endpoint;

    public function __construct(Curl $curl)
    {
        $this->curl = $curl;
    }

    public function setEnvironment($env)
    {
        $this->env = $env;
        $this->endpoint = sprintf('https://%s.plaid.com', $env);
    }

    public function getEndpoint()
    {
        return $this->endpoint;
    }
}
