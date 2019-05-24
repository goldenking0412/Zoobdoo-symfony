<?php

namespace Erp\SmartMoveBundle\Managers;

use Erp\SmartMoveBundle\Exceptions\SmartMoveManagerException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Erp\CoreBundle\Services\Curl;
use Erp\SmartMoveBundle\Models\SmartMoveModelInterface;

/**
 * Class SmartMoveAbstractManager
 *
 * @package Erp\SmartMoveBundle\Managers
 */
abstract class SmartMoveAbstractManager implements SmartMoveManagerInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var Curl
     */
    protected $curl;

    /**
     * @var string
     */
    protected static $apiEndpoint;

    /**
     * @var SmartMoveModelInterface
     */
    protected $model;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->curl = $this->container->get('erp.curl');
        $this->setApiEndpointUrl();
    }

    /**
     * Set model
     *
     * @param SmartMoveModelInterface $model
     *
     * @return $this
     */
    public function setModel(SmartMoveModelInterface $model = null)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @param string $type
     *
     * @return SmartMoveManagerException
     */
    public function proccess($type = '')
    {
        return new SmartMoveManagerException;
    }

    /**
     * Proccessing API responce
     *
     * @param array $response
     *
     * @return array
     */
    protected function proccessResponce(array $response)
    {
        $response = json_decode($response['body'], true);
        $result = ['status' => SmartMoveManagerInterface::STATUS_OK];

        if (!empty($response['Errors'])) {
            $result = [
                'status' => SmartMoveManagerInterface::STATUS_ERROR,
                'errors' => $response['Errors'][0]
            ];
        } else {
            $result['data'] = $response;
        }

        return $result;
    }

    /**
     * Generate Smart Move header
     *
     * @return string
     */
    protected function getHeader()
    {
        $apiPartnerId = $this->container->getParameter('smartmove.partner_id');
        $apiSecretKey = $this->container->getParameter('smartmove.api_secret_key');

        $serverTime = $this->getServerTime();
        $sSig = $apiPartnerId . $serverTime;
        $securityKey = mb_convert_encoding($apiSecretKey, "UTF8");
        $token = mb_convert_encoding(hash_hmac("sha1", $sSig, $securityKey, true), "BASE64");

        $header = 'Authorization: smartmovepartner partnerid="' . $apiPartnerId . '",servertime="'
            . $serverTime . '",securitytoken="' . $token . '"';

        return $header;
    }

    /**
     * Get Smart Move server time
     *
     * @return string
     */
    protected function getServerTime()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://smlegacygateway-integration.mysmartmove.com/RenterApi/v1/ServerTime');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        curl_close($ch);

        $serverTime = str_replace(['"', 'Z'], '', $response);
        $times = explode('.', $serverTime);
        $serverTime = $times[0];

        return $serverTime;
    }

    /**
     * Select api endpoint url
     *
     * @return $this
     */
    private function setApiEndpointUrl()
    {
        $endpointTest = $this->container->getParameter('smartmove.api.endpoint.test');
        $endpointLive = $this->container->getParameter('smartmove.api.endpoint.live');
        $isTestMode = $this->container->getParameter('smartmove.api.test_mode');

        self::$apiEndpoint = $endpointTest;
        if (!$isTestMode) {
            self::$apiEndpoint = $endpointLive;
        }

        return $this;
    }
}
