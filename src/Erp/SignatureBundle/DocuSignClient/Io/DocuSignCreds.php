<?php
namespace Erp\SignatureBundle\DocuSignClient\Io;

/**
 * Class DocuSignCreds
 *
 * @package Erp\SignatureBundle\DocuSignClient\Io
 */
class DocuSignCreds
{
    /**
     * The DocuSign Integrator's Key
     */
    private $integratorKey;

    /**
     *The Docusign Account Email
     */
    private $email;

    /**
     * The Docusign Account password or API password
     */
    private $password;

    /**
     * @param $integratorKey
     * @param $email
     * @param $password
     */
    public function __construct($integratorKey, $email, $password)
    {
        if (isset($integratorKey)) {
            $this->integratorKey = $integratorKey;
        }

        if (isset($email)) {
            $this->email = $email;
        }

        if (isset($password)) {
            $this->password = $password;
        }
    }

    /**
     * @param $integratorKey
     */
    public function setIntegratorKey($integratorKey)
    {
        $this->integratorKey = $integratorKey;
    }

    /**
     * @return mixed
     */
    public function getIntegratorKey()
    {
        return $this->integratorKey;
    }

    /**
     * @param $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        if (empty($this->integratorKey) || empty($this->email) || empty($this->password)) {
            return true;
        }

        return false;
    }
}
