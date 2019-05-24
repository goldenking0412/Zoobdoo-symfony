<?php
namespace Erp\SignatureBundle\DocuSignClient\Service;

/**
 * Class DocuSignResource
 *
 * @package Erp\SignatureBundle\DocuSignClient\Service
 */
abstract class DocuSignResource
{
    protected $service;
    protected $client;
    protected $curl;

    /**
     * @param DocuSignService $service
     */
    public function __construct(DocuSignService $service)
    {
        $this->service = $service;
        $this->client = $service->getClient();
        $this->curl = $service->getCUrl();
    }
}
