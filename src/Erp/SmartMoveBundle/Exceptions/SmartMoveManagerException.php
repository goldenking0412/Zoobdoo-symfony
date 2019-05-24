<?php

namespace Erp\SmartMoveBundle\Exceptions;

/**
 * Class SmartMoveManagerException
 *
 * @package Erp\SmartMoveBundle\SmartMove\Exceptions
 */
class SmartMoveManagerException extends \Exception
{
    public function __construct($msg = '')
    {
        parent::__construct($msg ? $msg : 'Wrong SmartMove Models usage');
    }
}
