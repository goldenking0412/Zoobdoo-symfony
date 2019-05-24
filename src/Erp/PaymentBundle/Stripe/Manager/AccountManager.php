<?php

namespace Erp\PaymentBundle\Stripe\Manager;

use Stripe\Account;

class AccountManager extends AbstractManager {

    public function create($params, $options = null) {
        return $this->client->sendAccountRequest('create', $params, $options);
    }

    public function retrieve($id, $options = null) {
        return $this->client->sendAccountRequest('retrieve', $id, $options);
    }

    public function update(Account $account, $params, $options = null) {
        return $this->client->sendUpdateRequest($account, $params, $options);
    }

}
