<?php

namespace Erp\SmartMoveBundle\Managers\API;

use Erp\SmartMoveBundle\Exceptions\SmartMoveManagerException;
use Erp\SmartMoveBundle\Managers\SmartMoveAbstractManager;

/**
 * Class RenterAPIManager
 *
 * @package Erp\SmartMoveBundle\SmartMove\Managers\SmartMoveManagers
 */
class RenterAPIManager extends SmartMoveAbstractManager
{
    /**
     * @param string $method
     *
     * @return mixed
     * @throws SmartMoveManagerException
     */
    public function proccess($method = '')
    {
        self::$apiEndpoint .= '/RenterApi/v1';

        switch ($method) {
            case self::METHOD_APPLICANT_STATUS:
                $responce = $this->getApplicantStatus();
                break;
            case self::METHOD_APPLICANT_ACCEPT_STATUS:
                $responce = $this->acceptApplicantStatus();
                break;
            case self::METHOD_RENTER_NEW:
                $responce = $this->createRenter();
                break;
            case self::METHOD_RETRIEVE_EXAM:
                $responce = $this->retrieveExam();
                break;
            case self::METHOD_EVALUATE_EXAM:
                $responce = $this->evaluateExam();
                break;
            case self::METHOD_GENERATE_REPORTS:
                $responce = $this->generateReports();
                break;
            default:
                $available = [
                    self::METHOD_APPLICANT_STATUS,
                    self::METHOD_APPLICANT_ACCEPT_STATUS,
                    self::METHOD_RENTER_NEW,
                    self::METHOD_RETRIEVE_EXAM,
                    self::METHOD_EVALUATE_EXAM,
                    self::METHOD_GENERATE_REPORTS,
                ];
                throw new SmartMoveManagerException(
                    sprintf(
                        'SmartMove method %s not found. Available methods are: %s',
                        $method,
                        implode(', ', $available)
                    )
                );
                break;
        }

        return $responce;
    }

    /**
     * Create new Renter
     *
     * @return array
     */
    protected function createRenter()
    {
        $url = self::$apiEndpoint . '/Renter';
        $params = $this->model->getSMRenter()->getInfo();
        $header = [$this->getHeader(), 'Content-Type: application/json', 'Content-Length: ' . strlen($params)];

        $response = $this->curl->setHeaders($header)->setPostParams($params)->execute($url);
        $response = $this->proccessResponce($response);

        return $response;
    }

    /**
     * Get applicant status
     *
     * @return array
     */
    protected function acceptApplicantStatus()
    {
        $smRenter = $this->model->getSMRenter();
        $url = '/ApplicationRenterStatus/Accept?email=' . $smRenter->getEmail()
            . '&applicationId=' . $smRenter->getSmApplicationId();
        $url = self::$apiEndpoint . $url;

        $params = json_encode([]);
        $header = [$this->getHeader(), 'Content-Type: application/json', 'Content-Length: ' . strlen($params)];

        $response = $this->curl->setHeaders($header)->setPostParams($params, 'PUT')->execute($url, 'PUT');
        $response = $this->proccessResponce($response);

        return $response;
    }

    /**
     * Retrieve renter exam
     *
     * @return array
     */
    protected function retrieveExam()
    {
        $url = self::$apiEndpoint . '/Exam/Retrieve';
        $params = $this->model->getSMRenter()->getInfo();
        $header = [$this->getHeader(), 'Content-Type: application/json', 'Content-Length: ' . strlen($params)];

        $response = $this->curl->setHeaders($header)->setPostParams($params)->execute($url);
        $response = $this->proccessResponce($response);

        return $response;
    }

    protected function evaluateExam()
    {
        $url = self::$apiEndpoint . '/Exam/Evaluate';
        $params = $this->model->getSMRenter()->getExams();
        $header = [$this->getHeader(), 'Content-Type: application/json', 'Content-Length: ' . strlen($params)];

        $response = $this->curl->setHeaders($header)->setPostParams($params, 'PUT')->execute($url, 'PUT');
        $response = $this->proccessResponce($response);

        return $response;
    }

    /**
     * Get applicant status
     *
     * @return array|mixed
     */
    protected function getApplicantStatus()
    {
        $smRenter = $this->model->getSMRenter();
        $url = '/ApplicationRenterStatus?email=' . $smRenter->getEmail()
            . '&applicationId=' . $smRenter->getSmApplicationId();
        $url = self::$apiEndpoint . $url;

        $header = [$this->getHeader()];

        $response = $this->curl->setHeaders($header)->execute($url);
        $response = $this->proccessResponce($response);

        return $response;
    }

    /**
     * Generate reports
     *
     * @return array|mixed
     */
    protected function generateReports()
    {
        $smRenter = $this->model->getSMRenter();
        $url = '/Report?email=' . $smRenter->getEmail() . '&applicationId=' . $smRenter->getSmApplicationId();
        $url = self::$apiEndpoint . $url;

        $params = json_encode([]);
        $header = [$this->getHeader(), 'Content-Type: application/json', 'Content-Length: ' . strlen($params)];

        $response = $this->curl->setHeaders($header)->setPostParams($params)->execute($url);
        $response = $this->proccessResponce($response);

        return $response;
    }
}
