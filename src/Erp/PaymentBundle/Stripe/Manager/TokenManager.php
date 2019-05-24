<?php

namespace Erp\PaymentBundle\Stripe\Manager;

class TokenManager extends AbstractManager {

    public function create($data, $options = null) {
        return $this->client->sendTokenRequest('create', $data, $options);
    }

}
