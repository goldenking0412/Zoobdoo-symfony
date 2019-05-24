<?php

namespace Erp\PaymentBundle\Stripe\Registry;

use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;

class ModelRegistry
{
    private $models = [];

    public function __construct(array $models = [])
    {
        $this->models = $models;
    }

    public function getModel($type)
    {
        if (!array_key_exists($type, $this->models)) {
            throw new ParameterNotFoundException($type);
        }

        return new $this->models[$type]();
    }
}