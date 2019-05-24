<?php

namespace Erp\PlaidBundle\Plaid\Service;

use Erp\CoreBundle\Services\Curl;

class Processor extends AbstractService
{
    /**
     * @var string
     */
    private $clientId;

    /**
     * @var string
     */
    private $secretKey;

    public function __construct(Curl $curl, $clientId, $secretKey)
    {
        parent::__construct($curl);
        $this->clientId = $clientId;
        $this->secretKey = $secretKey;
    }

    public function createBankAccountToken($accessToken, $accountId)
    {
        $params = [
            'client_id' => $this->clientId,
            'secret' => $this->secretKey,
            'access_token' => $accessToken,
            'account_id' => $accountId,
        ];
        $headers[] = 'Content-Type: application/json';

        $this->curl->setPostParams(json_encode($params))
            ->setHeaders($headers);

        $result = $this->curl->execute(sprintf('%s/processor/stripe/bank_account_token/create', $this->getEndpoint()));

        return $result;
    }
}
