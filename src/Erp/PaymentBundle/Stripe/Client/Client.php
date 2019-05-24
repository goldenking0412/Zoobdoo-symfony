<?php

namespace Erp\PaymentBundle\Stripe\Client;

use Stripe\Account;
use Stripe\Charge;
use Stripe\Customer;
use Stripe\Plan;
use Stripe\Stripe;
use Stripe\Token;
use Stripe\Subscription;

class Client {

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var null|string
     */
    protected $apiVersion;

    /**
     * @param string $apiKey
     * @param string $apiVersion
     */
    public function __construct($apiKey, $apiVersion = null) {
        $this->apiKey = $apiKey;
        $this->apiVersion = $apiVersion;

        Stripe::setApiKey($this->apiKey);

        if ($this->apiVersion) {
            Stripe::setApiVersion($this->apiVersion);
        }
    }

    public function sendChargeRequest($method, $params, $options = null) {
        try {
            $response = Charge::$method($params, $options);
        } catch (\Exception $e) {
            return new Response(null, $e);
        }

        return new Response($response);
    }

    public function sendTokenRequest($method, $params, $options = null) {
        try {
            $response = Token::$method($params, $options);
        } catch (\Exception $e) {
            return new Response(null, $e);
        }

        return new Response($response);
    }

    public function sendCustomerRequest($method, $params, $options = null) {
        try {
            $response = Customer::$method($params, $options);
        } catch (\Exception $e) {
            return new Response(null, $e);
        }

        return new Response($response);
    }

    public function sendCustomerSourceRequest(Customer $customer, $method, $params, $options = null) {
        try {
            $response = $customer->sources->{$method}($params, $options);
        } catch (\Exception $e) {
            return new Response(null, $e);
        }

        return new Response($response);
    }

    public function sendPlanRequest($method, $params, $options = null) {
        try {
            $response = Plan::$method($params, $options);
        } catch (\Exception $e) {
            return new Response(null, $e);
        }

        return new Response($response);
    }

    public function sendSubscriptionRequest($method, $params, $options = null) {
        try {
            $response = Subscription::$method($params, $options);
        } catch (\Exception $e) {
            return new Response(null, $e);
        }

        return new Response($response);
    }

    public function sendUpdateRequest($object, $params, $options = null) {
        foreach ($params as $prop => $value) {
            $object->{$prop} = $value;
        }

        try {
            $response = $object->save($options);
        } catch (\Exception $e) {
            return new Response(null, $e);
        }

        return new Response($response);
    }

    public function sendAccountRequest($method, $params, $options = null) {
        try {
            $response = Account::$method($params, $options);
        } catch (\Exception $e) {
            return new Response(null, $e);
        }

        return new Response($response);
    }

}
