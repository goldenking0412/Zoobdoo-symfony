<?php

namespace Erp\PaymentBundle\Stripe\Manager;

use Stripe\Plan;

class PlanManager extends AbstractManager {

    public function create($params, $options = null) {
        return $this->client->sendPlanRequest('create', $params, $options);
    }

    public function retrieve($id, $options = null) {
        return $this->client->sendPlanRequest('retrieve', $id, $options);
    }

    public function update(Plan $plan, $params, $options = null) {
        return $this->client->sendUpdateRequest($plan, $params, $options);
    }

}
