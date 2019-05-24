<?php
namespace Erp\SignatureBundle\DocuSignClient\Service;

use Erp\SignatureBundle\DocuSignClient\DocuSignClient;

/**
 * Class DocuSignRequestSignatureService
 *
 * @package Erp\SignatureBundle\Libs\DocuSignClient\Service
 */
class DocuSignRequestSignatureService extends DocuSignService
{
    /**
     * @var DocuSignRequestSignatureResource
     */
    public $signature;

    /**
     * Constructs the internal representation of the DocuSign Request Signature service.
     *
     * @param DocuSignClient $client
     */
    public function __construct(DocuSignClient $client)
    {
        parent::__construct($client);
        $this->signature = new DocuSignRequestSignatureResource($this);
    }
}
