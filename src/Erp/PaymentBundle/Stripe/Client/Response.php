<?php

namespace Erp\PaymentBundle\Stripe\Client;

use Stripe\Error\Base as Error;

class Response
{
    const REASON_CODE_INVALID = 'invalid';
    
    protected $content;
    protected $error;
    protected $errorMessage;
    protected $errorResponseCode;
    protected $errorReasonCode;

    public function __construct($content, $error = null)
    {
        $this->content = $content;
        $this->error = $error;

        if ($error instanceof Error) {
            $body = $error->getJsonBody();
            $err = $body['error'];

            $this->errorMessage = $error->getMessage();
            $this->errorResponseCode = $err['type'];

            if (array_key_exists('code', $err) && !empty($err['code'])) {
                $this->errorReasonCode = $err['code'];
            } else {
                $this->errorReasonCode  = self::REASON_CODE_INVALID;
            }
        } elseif ($error instanceof \Exception) {
            $this->errorMessage = $error->getMessage();
            $this->errorResponseCode = $error->getCode();
            $this->errorReasonCode = self::REASON_CODE_INVALID;
        }
    }

    public function isSuccess()
    {
        return !$this->error;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getError()
    {
        return $this->error;
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    public function getErrorResponseCode()
    {
        return $this->errorResponseCode;
    }

    public function getErrorReasonCode()
    {
        return $this->errorReasonCode;
    }
}