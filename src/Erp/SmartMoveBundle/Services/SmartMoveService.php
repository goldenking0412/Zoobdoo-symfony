<?php

namespace Erp\SmartMoveBundle\Services;

use Erp\SmartMoveBundle\Entity\SmartMoveRenter;
use Erp\SmartMoveBundle\Managers\SmartMoveManagerFactory;
use Erp\SmartMoveBundle\Managers\SmartMoveManagerInterface;
use Erp\SmartMoveBundle\Models\ManagerAPIModel;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Erp\SmartMoveBundle\Exceptions\SmartMoveManagerException;

class SmartMoveService
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getManager();
    }

    /**
     * Create new request to Smart Move
     *
     * @param SmartMoveRenter $smartMoveRenter
     *
     * @return array
     * @throws SmartMoveManagerException
     */
    public function newApplicantSmartMoveRequest(SmartMoveRenter $smartMoveRenter)
    {
        $result['status'] = false;
        $smartMoveRenter = $this->getRenterForAPI($smartMoveRenter);

        if ($smartMoveRenter->getSmApplicationId()) {
            $apiModel = $this->getManagerAPIModel($smartMoveRenter);

            $smManager = SmartMoveManagerFactory::getInstance(
                SmartMoveManagerInterface::TYPE_MANAGER,
                $this->container
            );
            $response = $smManager->setModel($apiModel)->proccess(SmartMoveManagerInterface::METHOD_APPLICANT_ADD);
            if ($response['status'] === SmartMoveManagerInterface::STATUS_OK) {
                $result['status'] = true;
                $result['data'] = $smartMoveRenter;
                $smartMoveRenter->setIsAddedAsApplicant(true);
            } else {
                $this->container->get('session')->getFlashBag()->add(
                    'alert_error',
                    $this->getTemplateForError() . $response['errors']
                );
            }

        } elseif ($smartMoveRenter->getSmPropertyId()) {
            $apiModel = $this->getManagerAPIModel($smartMoveRenter);

            $smManager = SmartMoveManagerFactory::getInstance(
                SmartMoveManagerInterface::TYPE_MANAGER,
                $this->container
            );
            $response = $smManager->setModel($apiModel)->proccess(SmartMoveManagerInterface::METHOD_APPLICATION_ADD);

            if ($response['status'] === SmartMoveManagerInterface::STATUS_OK) {
                $smartMoveRenter->setSmApplicationId($response['data']['ApplicationId']);
                $result['status'] = true;
                $result['data'] = $smartMoveRenter;
            } else {
                $this->container->get('session')->getFlashBag()->add(
                    'alert_error',
                    $this->getTemplateForError() . $response['errors']
                );
            }
        }
        $this->em->persist($smartMoveRenter);
        $this->em->flush();

        return $result;
    }

    /**
     * @param SmartMoveRenter $smartMoveRenter
     *
     * @throws SmartMoveManagerException
     */
    public function checkEmailStatus(SmartMoveRenter $smartMoveRenter)
    {
        $result['status'] = false;

        $apiModel = $this->getManagerAPIModel($smartMoveRenter);
        $smManager = SmartMoveManagerFactory::getInstance(
            SmartMoveManagerInterface::TYPE_RENTER,
            $this->container
        );
        $response = $smManager->setModel($apiModel)->proccess(SmartMoveManagerInterface::METHOD_APPLICANT_STATUS);
        if ($response['status'] === SmartMoveManagerInterface::STATUS_OK) {
            $result['status'] = true;
            $result['data'] = $smartMoveRenter;
        } else {
            $this->container->get('session')->getFlashBag()->add(
                'alert_error',
                $this->getTemplateForError() . $response['errors']
            );
        }
    }

    /**
     * Create new Renter via SmartMoveRenaterAPI
     *
     * @param SmartMoveRenter $smartMoveRenter
     *
     * @return array
     * @throws SmartMoveManagerException
     */
    public function createNewRenter(SmartMoveRenter $smartMoveRenter)
    {
        $result['status'] = false;

        $apiModel = $this->getManagerAPIModel($smartMoveRenter);
        $smManager = SmartMoveManagerFactory::getInstance(
            SmartMoveManagerInterface::TYPE_RENTER,
            $this->container
        );
        $response = $smManager->setModel($apiModel)->proccess(SmartMoveManagerInterface::METHOD_RENTER_NEW);
        if ($response['status'] === SmartMoveManagerInterface::STATUS_OK) {
            $result['status'] = true;
            $smartMoveRenter->setInfo(json_encode($response['data']));
            $result['data'] = $smartMoveRenter;
        } else {
            $this->container->get('session')->getFlashBag()->add(
                'alert_error',
                $this->getTemplateForError() . $response['errors']
            );
        }

        return $result;
    }

    /**
     * @param SmartMoveRenter $smartMoveRenter
     *
     * @throws SmartMoveManagerException
     */
    public function retrieveExamRenter(SmartMoveRenter $smartMoveRenter)
    {
        $result['status'] = false;

        $apiModel = $this->getManagerAPIModel($smartMoveRenter);
        $smManager = SmartMoveManagerFactory::getInstance(
            SmartMoveManagerInterface::TYPE_RENTER,
            $this->container
        );
        $response = $smManager->setModel($apiModel)->proccess(SmartMoveManagerInterface::METHOD_RETRIEVE_EXAM);
        if ($response['status'] == SmartMoveManagerInterface::STATUS_OK) {
            $result['status'] = true;
            $this->em->persist($smartMoveRenter->setExams(json_encode($response['data'])));
            $this->em->flush();

        } else {
            $this->container->get('session')->getFlashBag()->add(
                'alert_error',
                $this->getTemplateForError() . $response['errors']
            );
        }

        return $result;
    }

    /**
     * Evaluate Renter Exam
     *
     * @param SmartMoveRenter $smartMoveRenter
     *
     * @return array
     * @throws SmartMoveManagerException
     */
    public function evaluateExamRenter(SmartMoveRenter $smartMoveRenter)
    {
        $result['status'] = false;

        $apiModel = $this->getManagerAPIModel($smartMoveRenter);
        $smManager = SmartMoveManagerFactory::getInstance(
            SmartMoveManagerInterface::TYPE_RENTER,
            $this->container
        );
        $response = $smManager->setModel($apiModel)->proccess(SmartMoveManagerInterface::METHOD_EVALUATE_EXAM);
        if ($response['status'] == SmartMoveManagerInterface::STATUS_OK) {
            if (isset($response['data']['Evaluation']) && $response['data']['Evaluation'] == 'Pass') {
                $this->em->persist($smartMoveRenter->setIsExamComleted(true));
                $this->em->flush();
            }

            $result['status'] = true;
            $result['data'] = $smartMoveRenter;
        } else {
            $this->container->get('session')->getFlashBag()->add(
                'alert_error',
                $this->getTemplateForError() . $response['errors']
            );
        }

        return $result;
    }

    /**
     * Generate Renter Reports
     *
     * @param SmartMoveRenter $smartMoveRenter
     *
     * @return $this
     * @throws SmartMoveManagerException
     */
    public function generateRenterReports(SmartMoveRenter $smartMoveRenter)
    {
        $result['status'] = false;

        $apiModel = $this->getManagerAPIModel($smartMoveRenter);
        $smManager = SmartMoveManagerFactory::getInstance(
            SmartMoveManagerInterface::TYPE_RENTER,
            $this->container
        );
        $response = $smManager->setModel($apiModel)->proccess(SmartMoveManagerInterface::METHOD_GENERATE_REPORTS);
        if ($response['status'] == SmartMoveManagerInterface::STATUS_OK) {
            $result['status'] = true;
            $result['data'] = $smartMoveRenter;
        } else {
            $this->container->get('session')->getFlashBag()->add(
                'alert_error',
                $this->getTemplateForError() . $response['errors']
            );
        }

        return $result;
    }

    /**
     * @param SmartMoveRenter $smartMoveRenter
     *
     * @return array
     * @throws SmartMoveManagerException
     */
    public function getReports(SmartMoveRenter $smartMoveRenter)
    {
        $result['status'] = false;

        $apiModel = $this->getManagerAPIModel($smartMoveRenter);
        $smManager = SmartMoveManagerFactory::getInstance(
            SmartMoveManagerInterface::TYPE_MANAGER,
            $this->container
        );
        $response = $smManager->setModel($apiModel)->proccess(SmartMoveManagerInterface::METHOD_GET_REPORTS);
        if ($response['status'] == SmartMoveManagerInterface::STATUS_OK) {
            $applicants = $response['data']['Applicants'];

            $reports = [];
            foreach ($applicants as $key => $applicant) {
                if ($applicant['EmailAddress'] == $smartMoveRenter->getEmail()) {
                    if (is_array($applicants[$key]['CreditReport']) && count($applicants[$key]['CreditReport'])) {
                        $reports['CreditReport'] = $applicants[$key]['CreditReport'];
                    }

                    if (is_array($applicants[$key]['CriminalRecords']) && count($applicants[$key]['CriminalRecords'])) {
                        $reports['CriminalRecords'] = $applicants[$key]['CriminalRecords'];
                    }

                    if (is_array($applicants[$key]['EvictionRecords']) && count($applicants[$key]['EvictionRecords'])) {
                        $reports['EvictionRecords'] = $applicants[$key]['EvictionRecords'];
                    }
                    break;
                }
            }

            if ($reports) {
                $this->em->persist($smartMoveRenter->setReports(json_encode($reports)));
                $this->em->flush();
            }

            $result['status'] = true;
            $result['data'] = $smartMoveRenter;
        } else {
            $this->container->get('session')->getFlashBag()->add(
                'alert_error',
                $this->getTemplateForError() . $response['errors']
            );
        }

        return $result;
    }

    /**
     * Marks application as accepted by a renter
     *
     * @param SmartMoveRenter $smartMoveRenter
     *
     * @throws SmartMoveManagerException
     */
    public function acceptApplicationByRenter(SmartMoveRenter $smartMoveRenter)
    {
        $result['status'] = false;

        $apiModel = $this->getManagerAPIModel($smartMoveRenter);
        $smManager = SmartMoveManagerFactory::getInstance(
            SmartMoveManagerInterface::TYPE_RENTER,
            $this->container
        );
        $response = $smManager->setModel($apiModel)->proccess(
            SmartMoveManagerInterface::METHOD_APPLICANT_ACCEPT_STATUS
        );
        if ($response['status'] === SmartMoveManagerInterface::STATUS_OK) {
            $smartMoveRenter->setIsAccepted(true);

            $result['status'] = true;
            $result['data'] = $smartMoveRenter;
        } else {
            $this->container->get('session')->getFlashBag()->add(
                'alert_error',
                $this->getTemplateForError() . $response['errors']
            );
        }

        return $result;
    }

    /**
     * Get model for SmartMove API
     *
     * @param SmartMoveRenter $smartMoveRenter
     *
     * @return SmartMoveRenter
     */
    protected function getRenterForAPI(SmartMoveRenter $smartMoveRenter)
    {
        if (!$smartMoveRenter->getSmPropertyId() && !$smartMoveRenter->getSmApplicationId()) {
            /** @var $existSMRenter \Erp\SmartMoveBundle\Entity\SmartMoveRenter */
            $existSMRenter = $this->em->getRepository('ErpSmartMoveBundle:SmartMoveRenter')
                ->getByManager($smartMoveRenter->getManager());

            if ($existSMRenter) {
                $smartMoveRenter->setSmPropertyId($existSMRenter->getSmPropertyId())
                    ->setSmApplicationId($existSMRenter->getSmApplicationId());
                $this->em->persist($smartMoveRenter);
                $this->em->flush();
            } else {
                $propertyResponse = $this->createSmartMoveProperty($smartMoveRenter);
                if ($propertyResponse['status']) {
                    $smartMoveRenter = $propertyResponse['data'];
                }
            }
        }

        return $smartMoveRenter;
    }

    /**
     * @param SmartMoveRenter $smartMoveRenter
     *
     * @return array
     * @throws SmartMoveManagerException
     */
    public function createSmartMoveProperty(SmartMoveRenter $smartMoveRenter)
    {
        $apiModel = $this->getManagerAPIModel($smartMoveRenter);

        $smManager = SmartMoveManagerFactory::getInstance(SmartMoveManagerInterface::TYPE_MANAGER, $this->container);
        $responce = $smManager->setModel($apiModel)->proccess(SmartMoveManagerInterface::METHOD_PROPERTY_ADD);

        $result['status'] = true;
        if ($responce['status'] === SmartMoveManagerInterface::STATUS_OK) {
            $smartMoveRenter->setSmPropertyId($responce['data']['PropertyId']);

            $this->em->persist($smartMoveRenter);
            $this->em->flush();

            $result['data'] = $smartMoveRenter;
        } else {
            $result['status'] = false;
            $this->container->get('session')->getFlashBag()->add(
                'alert_error',
                $this->getTemplateForError() . $responce['errors']
            );
        }

        return $result;
    }

    /**
     * Get ManagerAPIModel
     *
     * @param SmartMoveRenter $smartMoveRenter
     *
     * @return ManagerAPIModel
     */
    public function getManagerAPIModel(SmartMoveRenter $smartMoveRenter)
    {
        $apiModel = new ManagerAPIModel();
        $apiModel->setSMRenter($smartMoveRenter)
            ->setEmail($smartMoveRenter->getEmail())
            ->setManager($smartMoveRenter->getManager());

        return $apiModel;
    }

    /**
     * Get First part of error message
     *
     * @return string
     */
    private function getTemplateForError()
    {
        return 'Background Check/Credit Check: ';
    }
}
