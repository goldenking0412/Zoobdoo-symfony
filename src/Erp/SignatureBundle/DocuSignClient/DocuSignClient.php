<?php
namespace Erp\SignatureBundle\DocuSignClient;

use Erp\SignatureBundle\DocuSignClient\Io\DocuSignCreds;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class DocuSignClient
 *
 * @package Erp\SignatureBundle\DocuSignClient
 */
class DocuSignClient
{
    /**
     * The DocuSign Credentials
     *
     * @var DocuSignCreds
     */
    public $creds;

    /**
     * The version of DocuSign API
     */
    public $version;

    /**
     * The DocuSign Environment
     */
    public $environment;

    /**
     * The base url of the DocuSign Account
     */
    public $baseURL;

    /**
     * The DocuSign Account Id
     */
    public $accountID;

    /**
     * The Curl class
     */
    public $curl;

    /**
     * The flag indicating if it has multiple DocuSign accounts
     */
    public $hasMultipleAccounts = false;

    /**
     * @var bool
     */
    public $hasError = false;

    /**
     * @var string
     */
    public $errorMessage = '';

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Return client configuration params
     *
     * @return array
     */
    public function getConfig()
    {
        return [
            'version'        => 'v2',
            'integrator_key' => $this->container->getParameter('docusign_integrator_key'),
            'email'          => $this->container->getParameter('docusign_email'),
            'password'       => $this->container->getParameter('docusign_password'),
            'environment'    => $this->container->getParameter('docusign_environment'),
            'account_id'     => $this->container->getParameter('docusign_account_id'),
        ];
    }

    /**
     * Init client configs
     *
     * @return $this
     */
    public function initConfig()
    {
        $config = $this->getConfig();

        $this->creds = new DocuSignCreds(
            $config['integrator_key'],
            $config['email'],
            $config['password']
        );

        if (isset($config['version'])) {
            $this->version = $config['version'];
        }

        if (isset($config['environment'])) {
            $this->environment = $config['environment'];
        }

        if (!empty($config['account_id'])) {
            $this->accountID = $config['account_id'];
        }

        $this->curl = $this->container->get('erp.curl');

        if (!$this->creds->isEmpty()) {
            self::authenticate();
        } else {
            // you can potentially make this a warning instead of error if you'd like, depends on
            // how you want to handle missing api credentials...
            $this->hasError = true;
            $this->errorMessage = 'One or more missing config settings found.' .
                'Please check config.php, or pass in required credentials to DocuSignClient class constructor.';
        }

        return $this;
    }

    /**
     * @return mixed|void
     */
    public function authenticate()
    {
        $this->baseURL = 'https://' . $this->environment . '.docusign.net/restapi/' . $this->version;
        $url = $this->baseURL . '/login_information';

        try {
            $this->curl->setHeaders($this->getHeaders())->execute($url);
            $response = $this->curl->getBodyResponse(true);
        } catch (Exception $e) {
            $this->hasError = true;
            $this->errorMessage = $e->getMessage();

            return;
        }

        if (count($response->loginAccounts) > 1) {
            $this->hasMultipleAccounts = true;
        }

        $defaultBaseURL = '';
        $defaultAccountID = '';

        foreach ($response->loginAccounts as $account) {
            if (!empty($this->accountID)) {
                if ($this->accountID == $account->accountId) {
                    $this->baseURL = $account->baseUrl;
                    break;
                }
            }

            if ($account->isDefault === true) {
                $defaultBaseURL = $account->baseUrl;
                $defaultAccountID = $account->accountId;
            }
        }

        if (empty($this->baseURL)) {
            $this->baseURL = $defaultBaseURL;
            $this->accountID = $defaultAccountID;
        }

        return $response;
    }

    /**
     * @return DocuSignCreds
     */
    public function getCreds()
    {
        return $this->creds;
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return mixed
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * @return mixed
     */
    public function getBaseURL()
    {
        return $this->baseURL;
    }

    /**
     * @return mixed
     */
    public function getAccountID()
    {
        return $this->accountID;
    }

    /**
     * @return mixed
     */
    public function getCUrl()
    {
        return $this->curl;
    }

    /**
     * @return bool
     */
    public function hasMultipleAccounts()
    {
        return $this->hasMultipleAccounts;
    }

    /**
     * @return bool
     */
    public function hasError()
    {
        return $this->hasError;
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * @param string $accept
     * @param string $contentType
     *
     * @return array
     */
    public function getHeaders($accept = 'Accept: application/json', $contentType = 'Content-Type: application/json')
    {
        return [
            'X-DocuSign-Authentication: <DocuSignCredentials><Username>' .
            $this->creds->getEmail() . '</Username><Password>' .
            $this->creds->getPassword() . '</Password><IntegratorKey>' .
            $this->creds->getIntegratorKey() . '</IntegratorKey></DocuSignCredentials>',
            $accept,
            $contentType
        ];
    }

    /**
     * @param        $soboUser
     * @param string $accept
     * @param string $contentType
     *
     * @return array
     */
    public function getSoboHeaders(
        $soboUser,
        $accept = 'Accept: application/json',
        $contentType = 'Content-Type: application/json'
    ) {
        return [
            'X-DocuSign-Authentication: <DocuSignCredentials><SendOnBehalfOf>' .
            $soboUser . '</SendOnBehalfOf><Username>' . $this->creds->getEmail() .
            '</Username><Password>' . $this->creds->getPassword() .
            '</Password><IntegratorKey>' . $this->creds->getIntegratorKey() .
            '</IntegratorKey></DocuSignCredentials>',
            $accept,
            $contentType
        ];
    }
}
