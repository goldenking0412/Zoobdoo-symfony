<?php

namespace Erp\PaymentBundle\Stripe\Manager;

class ChargeManager extends AbstractManager {

    public function create($params, $options = null) {
        return $this->client->sendChargeRequest('create', $params, $options);
    }

}
