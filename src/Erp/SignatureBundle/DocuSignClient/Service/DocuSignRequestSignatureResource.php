<?php

namespace Erp\SignatureBundle\DocuSignClient\Service;

/**
 * Class DocuSignRequestSignatureResource
 */
class DocuSignRequestSignatureResource extends DocuSignResource
{
    /**
     * @param \Erp\SignatureBundle\DocuSignClient\Service\DocuSignService $service
     */
    public function __construct(DocuSignService $service)
    {
        parent::__construct($service);
    }

    /**
     * @param        $emailSubject
     * @param        $emailBlurb
     * @param string $status
     * @param array  $documents
     * @param array  $recipients
     * @param array  $eventNotifications
     * @param array  $options
     *
     * @return mixed
     */
    public function createEnvelopeFromDocument(
        $emailSubject,
        $emailBlurb,
        $status = 'created',
        $documents = [],
        $recipients = [],
        $eventNotifications = [],
        $options = []
    ) {
        $url = $this->client->getBaseURL() . '/accounts/' . $this->client->getAccountID() . '/envelopes';

        $headers = $this->client->getHeaders(
            'Accept: application/json',
            'Content-Type: multipart/form-data;boundary=myboundary'
        );

        $doc = [];
        $contentDisposition = '';
        foreach ($documents as $document) {
            array_push(
                $doc,
                [
                    'name' => $document->getName(),
                    'documentId' => $document->getId()
                ]
            );

            $contentDisposition .= "--myboundary\r\n" .
                "Content-Type:application/pdf\r\n" .
                "Content-Disposition: file; filename=\"" .
                $document->getName() .
                "\"; documentid=" .
                $document->getId() .
                "\r\n" .
                "\r\n" .
                $document->getContent() .
                "\r\n";
        }

        $data = [
            'emailSubject' => $emailSubject,
            'emailBlurb' => $emailBlurb,
            'documents' => $doc,
            'status' => $status
        ];

        if (!empty($options)) {
            $data = array_merge($data, $options);
        }

        if (isset($recipients) && sizeof($recipients) > 0) {
            $recipientsList = [];
            foreach ($recipients as $recipient) {
                $recipientsList[$recipient->getType()][] = [
                    'routingOrder' => $recipient->getRoutingOrder(),
                    'recipientId' => $recipient->getId(),
                    'name' => $recipient->getName(),
                    'email' => $recipient->getEmail(),
                    'clientUserId' => $recipient->getClientId(),
                    'tabs' => $recipient->getTabs(),
                ];
            }
            $data['recipients'] = $recipientsList;
        }

        if (isset($eventNotifications) && sizeof($eventNotifications) > 0) {
            $data['eventNotification'] = $eventNotifications->toArray();
        }

        $dataString = json_encode($data);
        $data = "\r\n" .
            "\r\n" .
            "--myboundary\r\n" .
            "Content-Type: application/json\r\n" .
            "Content-Disposition: form-data\r\n" .
            "\r\n" .
            $dataString .
            "\r\n" .
            $contentDisposition .
            "--myboundary--";

        $this->curl->setHeaders($headers)->setPostParams($data)->execute($url);
        $response = $this->curl->getBodyResponse(true);

        return $response;
    }


    public function createEnvelopeFromDocumentNew(
        $emailSubject,
        $emailBlurb,
        $status = 'created',
        $documents = [],
        $recipients = [],
        $eventNotifications = [],
        $options = []
    ) {
        $doc = [];
        $contentDisposition = '';
        foreach ($documents as $document) {
            array_push(
                $doc,
                [
                    'name' => $document->getName(),
                    'documentId' => $document->getId()
                ]
            );
            $contentDisposition .= "--myboundary\r\n" .
                "Content-Type:application/pdf\r\n" .
                "Content-Disposition: file; filename=\"" .
                $document->getName() .
                "\"; documentid=" .
                $document->getId() .
                "\r\n" .
                "\r\n" .
                $document->getContent() .
                "\r\n";
        }

        $data = [
            'emailSubject' => $emailSubject,
            'emailBlurb' => $emailBlurb,
            'documents' => $doc,
            'status' => $status
        ];
        $data = array_merge($data, $options);

        if ($recipients) {
            $recipientsList = [];
            foreach ($recipients as $recipient) {
                $recipientsList[$recipient->getType()][] = [
                    'routingOrder' => $recipient->getRoutingOrder(),
                    'recipientId' => $recipient->getId(),
                    'name' => $recipient->getName(),
                    'email' => $recipient->getEmail(),
                    'clientUserId' => $recipient->getClientId(),
                    'tabs' => $recipient->getTabs(),
                ];
            }
            $data['recipients'] = $recipientsList;
        }

        if ($eventNotifications) {
            $data['eventNotification'] = $eventNotifications;
        }

        $headers = $this->client->getHeaders(
            'Accept: application/json',
            'Content-Type: multipart/form-data;boundary=myboundary'
        );
        $data = json_encode($data);
        $data = "\r\n" .
            "\r\n" .
            "--myboundary\r\n" .
            "Content-Type: application/json\r\n" .
            "Content-Disposition: form-data\r\n" .
            "\r\n" .
            $data .
            "\r\n" .
            $contentDisposition .
            "--myboundary--";

        $url = $this->client->getBaseURL().'/accounts/'.$this->client->getAccountID().'/envelopes';
        $this->curl->setHeaders($headers)->setPostParams($data)->execute($url);
        $response = $this->curl->getBodyResponse(true);

        return $response;
    }

    public function createCorrectLink($envelopeId, $returnUrl = 'http://www.docusign.com/devcenter')
    {
        $data = [
            'returnUrl' => $returnUrl,
        ];
        $data = json_encode($data);
        $headers = $this->client->getHeaders('Content-Length: '.strlen($data), 'Content-Type: application/json');
        $url = $this->client->getBaseURL().'/accounts/'.$this->client->getAccountID().'/envelopes'.'/'. $envelopeId.'/views/correct';
        $this->curl->setHeaders($headers)->setPostParams($data)->execute($url);
        $response = $this->curl->getBodyResponse(true);

        return $response;
    }
}
