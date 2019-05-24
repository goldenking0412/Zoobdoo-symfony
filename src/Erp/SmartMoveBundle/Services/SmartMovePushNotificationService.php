<?php

namespace Erp\SmartMoveBundle\Services;

use Doctrine\ORM\EntityManager;
use Erp\CoreBundle\EmailNotification\EmailNotificationFactory;
use Erp\CoreBundle\Services\EmailNotification;
use Erp\SmartMoveBundle\Entity\SmartMoveRenter;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SmartMovePushNotificationService
 * @package Erp\SmartMoveBundle\Services
 */
class SmartMovePushNotificationService
{
    const ON_RENTER_ACCEPT        = 'renter_accept';
    const ON_RENTER_DECLAIN       = 'renter_declain';
    const ON_APPLICATION_COMPLETE = 'application_complete';

    /**
     * @var EmailNotification
     */
    protected $emailNotification;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @param EmailNotification $emailNotification
     */
    public function setEmailNotification(EmailNotification $emailNotification)
    {
        $this->emailNotification = $emailNotification;
    }

    /**
     * @return EmailNotification
     */
    public function getEmailNotification()
    {
        return $this->emailNotification;
    }

    /**
     * @param EntityManager $entityManager
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @param Request $request
     *
     * @return $this
     */
    public function processPushRequest(Request $request)
    {
        $requestBody = $request->getContent();
        $requestParams = $request->request->all();

        $this->savePushNoificationLogs($requestBody, json_encode($requestParams));

        /*$notificationType = $this->getNotificationType($requestBody);

        $applicationId = $this->getApplicationId($requestBody);

        $eventText = $this->getEventText($requestBody);

        $this->proccesPushNotification($notificationType, $applicationId, $eventText);*/

        return $this;
    }

    /**
     * @param $requestBody
     * @return $this
     */
    private function savePushNoificationLogs($requestBody, $requestParams)
    {
        $email = 'push-'. (new \DateTime())->format('Y-m-d-H-i-s').'@mail.com';
        $lastSMRenter = $this->getEntityManager()->getRepository('ErpSmartMoveBundle:SmartMoveRenter')
            ->getLastManager();

        $smRenter = new SmartMoveRenter();
        $smRenter->setEmail($email)
            ->setInfo($requestBody)
            ->setPersonalToken($requestParams)
            ->setManager($lastSMRenter->getManager());
        $this->getEntityManager()->persist($smRenter);
        $this->getEntityManager()->flush();

        return $this;
    }

    /**
     * @param string $notificationType
     * @param int    $applicationId
     * @param string $eventText
     *
     * @return $this
     */
    private function proccesPushNotification($notificationType, $applicationId, $eventText)
    {
        $smRenter = $this->getSMRenterByApplicationId($applicationId);

        if ($smRenter) {
            switch ($notificationType) {
                case self::ON_RENTER_ACCEPT:
                    $this->onRenterAccept($smRenter, $eventText);
                    break;
                case self::ON_RENTER_DECLAIN:
                    $this->onRenterDecline($smRenter, $eventText);
                    break;
                case self::ON_APPLICATION_COMPLETE:
                    $this->onApplicationComplete($smRenter, $eventText);
                    break;
                default:
                    break;
            }
        }

        return $this;
    }

    /**
     * @param SmartMoveRenter $smartMoveRenter
     * @param string          $eventText
     *
     * @return $this
     */
    private function onRenterAccept(SmartMoveRenter $smartMoveRenter, $eventText)
    {
        $emailParams = [
            'sendTo' => $smartMoveRenter->getManager()->getEmail(),
            'text'   => $eventText,
            'title'  => 'Renter accept application'
        ];
        $this->sendEmailNotification($emailParams);

        return $this;
    }

    /**
     * @param SmartMoveRenter $smartMoveRenter
     * @param string          $eventText
     *
     * @return $this
     */
    private function onRenterDecline(SmartMoveRenter $smartMoveRenter, $eventText)
    {
        $emailParams = [
            'sendTo' => $smartMoveRenter->getManager()->getEmail(),
            'text'   => $eventText,
            'title'  => 'Renter decline application'
        ];
        $this->sendEmailNotification($emailParams);

        return $this;
    }

    /**
     * @param SmartMoveRenter $smartMoveRenter
     * @param string          $eventText
     *
     * @return $this
     */
    private function onApplicationComplete(SmartMoveRenter $smartMoveRenter, $eventText)
    {
        $emailParams = [
            'sendTo' => $smartMoveRenter->getManager()->getEmail(),
            'text'   => $eventText,
            'title'  => 'Renter complete IDMA exams'
        ];
        $this->sendEmailNotification($emailParams);

        return $this;
    }

    /**
     * @param int $applicationId
     *
     * @return \Erp\SmartMoveBundle\Entity\SmartMoveRenter|null
     */
    private function getSMRenterByApplicationId($applicationId)
    {
        $smRenter = $this->getEntityManager()->getRepository('ErpSmartMoveBundle:SmartMoveRenter')->findOneBy(
            ['smApplicationId' => (int)$applicationId]
        );

        return $smRenter;
    }

    /**
     * @param array $emailParams
     *
     * @return $this
     */
    private function sendEmailNotification($emailParams)
    {
        $this->getEmailNotification()->sendEmail(EmailNotificationFactory::TYPE_SM_PUSH_NOTIFICATION, $emailParams);

        return $this;
    }

    /**
     * @param string $requestBody
     *
     * @return string
     */
    private function getNotificationType($requestBody)
    {
        return '';
    }

    /**
     * @param string $requestBody
     *
     * @return int
     */
    private function getApplicationId($requestBody)
    {
        return 1;
    }

    /**
     * @param string $requestBody
     *
     * @return string
     */
    private function getEventText($requestBody)
    {
        return '';
    }
}
