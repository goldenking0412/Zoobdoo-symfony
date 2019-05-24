<?php

namespace Erp\StripeBundle\Entity;

use Erp\PaymentBundle\Stripe\Client\Response;
use Stripe\ApiResource;

class ApiManager {

    public function __construct($apiKey, $apiVersion = null) {
        \Stripe\Stripe::setApiKey($apiKey);

        if ($apiVersion) {
            \Stripe\Stripe::setApiVersion($apiVersion);
        }
    }

    public function callStripeApi($endpoint, $action, array $arguments) {
        try {
            switch (count($arguments)) {
                case 1:
                    $options = empty($arguments['options']) ? null : $arguments['options'];
                    $response = $endpoint::$action($options);
                    break;
                case 2:
                    if (isset($arguments['id'])) {
                        $options = empty($arguments['options']) ? null : $arguments['options'];
                        $response = $endpoint::$action($arguments['id'], $options);
                    } else {
                        $params = empty($arguments['params']) ? null : $arguments['params'];
                        $options = empty($arguments['options']) ? null : $arguments['options'];
                        $response = $endpoint::$action($params, $options);
                    }
                    break;
                case 3:
                    $params = empty($arguments['params']) ? null : $arguments['params'];
                    $options = empty($arguments['options']) ? null : $arguments['options'];
                    $response = $endpoint::$action($arguments['id'], $params, $options);
                    break;
                default:
                    throw new \RuntimeException('The arguments passed don\'t correspond to the allowed number. Please, review them.');
            }
        } catch (\Exception $e) {
            return new Response(null, $e);
        }

        return new Response($response);
    }

    public function callStripeObject(ApiResource $object, $method, array $arguments = []) {
        try {
            switch (count($arguments)) {
                case 0:
                    $response = $object->$method();
                    break;
                case 1:
                    $response = $object->$method($arguments[0]);
                    break;
                case 2:
                    $params = empty($arguments['params']) ? null : $arguments['params'];
                    $options = empty($arguments['options']) ? null : $arguments['options'];
                    $response = $object->$method($params, $options);
                    break;
                default:
                    throw new \RuntimeException('The arguments passed don\'t correspond to the allowed number. Please, review them.');
            }
        } catch (\Exception $e) {
            return new Response(null, $e);
        }

        return new Response($response);
    }

}
