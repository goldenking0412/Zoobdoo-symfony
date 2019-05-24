<?php

namespace Erp\CoreBundle\Exception;

use Exception;

/**
 * Class UserNotFoundException
 *
 * @package Erp\CoreBundle\Exception
 */
class UserNotFoundException extends Exception
{
    public function __construct()
    {
        parent::__construct('Unable to find User entity');
    }
}
