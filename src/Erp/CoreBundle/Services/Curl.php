<?php
namespace Erp\CoreBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;

class Curl
{
    /**
     * @var string
     */
    public $body;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * resource a cURL handle
     *
     * @var resource
     */
    protected $ch = null;

    /**
     * Construct method
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Set request POST params
     *
     * @param string $params
     * @param string $customRequest
     *
     * @return $this
     */
    public function setPostParams($params = '', $customRequest = 'POST')
    {
        $this->initCurl();
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, $customRequest);
        curl_setopt($this->ch, CURLOPT_POST, true);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $params);

        return $this;
    }

    /**
     * Set request headers
     *
     * @param array $headers
     *
     * @return $this
     */
    public function setHeaders(array $headers)
    {
        $this->initCurl();
        curl_setopt($this->ch, CURLOPT_HEADER, true);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);

        return $this;
    }

    /**
     * @param string        $url
     * @param null|string   $customRequest
     *
     * @return mixed
     */
    public function execute($url, $customRequest = null)
    {
        $this->initCurl();
        curl_setopt($this->ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);

        if ($customRequest) {
            curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, $customRequest);
        }

        $isHttps = strpos($url, 'https');
        if ($isHttps !== false) {
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
        }

        $response = curl_exec($this->ch);
        $headerSize = curl_getinfo($this->ch, CURLINFO_HEADER_SIZE);

        $result['code'] = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
        $result['header'] = substr($response, 0, $headerSize);
        $result['body'] = substr($response, $headerSize);

        $this->setBodyResponse($result['body']);

        curl_close($this->ch);

        return $result;
    }

    /**
     * @param bool|false $jsonDecode
     *
     * @return string|array
     */
    public function getBodyResponse($jsonDecode = false)
    {
        return ($jsonDecode) ? json_decode($this->body) : $this->body;
    }

    /**
     * @param $body
     */
    private function setBodyResponse($body)
    {
        $this->body = $body;
    }

    /**
     * Init curl resource
     *
     * @return $this
     */
    private function initCurl()
    {
        if (!is_resource($this->ch)) {
            $this->ch = curl_init();
        }

        return $this;
    }
}
