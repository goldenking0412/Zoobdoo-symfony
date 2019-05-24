<?php

namespace Erp\PlaidBundle\Plaid\Service;

use Erp\CoreBundle\Services\Curl;

class Item extends AbstractService
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

    public function exchangePublicToken($publicToken)
    {
        $params = [
            'client_id' => $this->clientId,
            'secret' => $this->secretKey,
            'public_token' => $publicToken,
        ];
        $headers[] = 'Content-Type: application/json';

        $this->curl->setPostParams(json_encode($params))
            ->setHeaders($headers);

        $result = $this->curl->execute(sprintf('%s/item/public_token/exchange', $this->getEndpoint()));

        return $result;
    }
}
