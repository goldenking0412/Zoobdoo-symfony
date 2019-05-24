<?php

namespace Erp\StripeBundle\Registry;

use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;

class FormRegistry {

    private $forms = [];

    public function __construct(array $formTypes = []) {
        $this->forms = $formTypes;
    }

    public function getForm($type) {
        if (!array_key_exists($type, $this->forms)) {
            throw new ParameterNotFoundException($type);
        }

        return $this->forms[$type];
    }

}
