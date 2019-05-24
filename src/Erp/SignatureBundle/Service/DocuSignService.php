<?php
namespace Erp\SignatureBundle\Service;

use Erp\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Erp\SignatureBundle\DocuSignClient\Service\DocuSignDocument;
use Erp\SignatureBundle\DocuSignClient\Service\DocuSignRecipient;
use Erp\SignatureBundle\DocuSignClient\Service\DocuSignRequestSignatureService;
use Erp\SignatureBundle\DocuSignClient\Exception\DocuSignException;
use Erp\CoreBundle\Entity\Document;

class DocuSignService
{
    const DOCUSIGN_STATUS_SENT = 'sent';
    const DOCUSIGN_STATUS_CREATED = 'created';

    /**
     * @var object DocuSignRequestSignatureService
     */
    public $service;

    /**
     * @var ContainerInterface
     */
    protected $container;

    private $client;

    /**
     * Construct method
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Create and sent envelope
     *
     * @param Document $document
     * @param          $email
     *
     * @return mixed
     */
    public function createEnvelopeFromDocument(Document $document, $email)
    {
        $client = $this->container->get('erp.signature.docusign.client');
        if ($client->hasError()) {
            throw new NotFoundHttpException($client->getErrorMessage());
        }

        $this->service = new DocuSignRequestSignatureService($client);
        $baseDir = $document->getUploadBaseDir($this->container);
        $file = $baseDir . $document->getPath() . '/' . $document->getName();

        if (!file_exists($file)) {
            throw new NotFoundHttpException('File not found');
        }

        $documents = [new DocuSignDocument($document->getOriginalName(), 1, file_get_contents($file))];
        $signers = [new DocuSignRecipient(1, 1, $email, $email)];
        $eventNotifications = [];

        $response = $this->service->signature->createEnvelopeFromDocument(
            'The document to be signed',
            'The document to be signed',
            self::DOCUSIGN_STATUS_SENT,
            $documents,
            $signers,
            $eventNotifications
        );

        return $response;
    }

    public function createCorrectLink($envelopId, User $recipient)
    {
        $router = $this->container->get('router');
        $returnUrl = $router->generate('erp_user_documentation', ['toUserId' => $recipient->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        if (!$this->service) {
            $client = $this->container->get('erp.signature.docusign.client');
            if ($client->hasError()) {
                throw new NotFoundHttpException($client->getErrorMessage());
            }

            $this->service = new DocuSignRequestSignatureService($client);
        }

        $response = $this->service->signature->createCorrectLink($envelopId, $returnUrl);

        return $response->url;
    }

    //TODO Refactor it
    public function createEnvelopeFromDocumentNew(Document $document, User $sender, User $recipient)
    {
        $client = $this->container->get('erp.signature.docusign.client');
        if ($client->hasError()) {
            throw new NotFoundHttpException($client->getErrorMessage());
        }

        $this->service = new DocuSignRequestSignatureService($client);
        $baseDir = $document->getUploadBaseDir($this->container);
        $file = $baseDir.$document->getPath().'/'.$document->getName();

        if (!file_exists($file)) {
            throw new NotFoundHttpException('File not found');
        }

        $documents = [new DocuSignDocument($document->getOriginalName(), 1, file_get_contents($file))];
        $signers = [
            new DocuSignRecipient(1, 1, $sender->getEmail(), $sender->getEmail()),
            new DocuSignRecipient(2, 2, $recipient->getEmail(), $recipient->getEmail()),
        ];

        $router = $this->container->get('router');
        $webhookUrl = $router->generate('erp_signature_webhook_notify', [], UrlGeneratorInterface::ABSOLUTE_URL);

        $response = $this->service->signature->createEnvelopeFromDocumentNew(
            'The document to be signed',
            'The document to be signed',
            self::DOCUSIGN_STATUS_SENT,
            $documents,
            $signers,
            [
                'url' => $webhookUrl,
                'envelopeEvents' => [
                    ['envelopeEventStatusCode' => 'sent'],
                    ['envelopeEventStatusCode' => 'completed']
                ],
                'recipientEvents' => [
                    ['envelopeEventStatusCode' => 'sent'],
                    ['envelopeEventStatusCode' => 'completed']
                ],
            ]
        );

        if (isset($response->status) && $response->status !== 'sent') {
            throw new DocuSignException('An occurred error.');
        }

        return $response;
    }
}
