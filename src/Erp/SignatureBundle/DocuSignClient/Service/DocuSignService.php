<?php
namespace Erp\SignatureBundle\DocuSignClient\Service;

use Erp\SignatureBundle\DocuSignClient\DocuSignClient;

/**
 * Class DocuSignService
 *
 * @package Erp\SignatureBundle\DocuSignClient\Service
 */
abstract class DocuSignService
{
    protected $client;
    protected $curl;

    /**
     * @param DocuSignClient $client
     */
    public function __construct(DocuSignClient $client)
    {
        $this->client = $client;
        $this->curl = $client->getCUrl();
    }

    /**
     * @return DocuSignClient
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return \Erp\SignatureBundle\DocuSignClient\Io\DocuSignCurlIO
     */
    public function getCUrl()
    {
        return $this->curl;
    }
}
