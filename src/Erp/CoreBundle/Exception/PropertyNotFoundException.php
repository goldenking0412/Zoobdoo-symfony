<?php

namespace Erp\CoreBundle\Exception;

use Exception;

/**
 * Class PropertyNotFoundException
 *
 * @package Erp\CoreBundle\Exception
 */
class PropertyNotFoundException extends Exception
{
    public function __construct()
    {
        parent::__construct('Unable to find Property');
    }
}
