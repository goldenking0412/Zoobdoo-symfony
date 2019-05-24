<?php

namespace Erp\NotificationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Erp\PropertyBundle\Entity\Property;
use Erp\NotificationBundle\Entity\UserNotification;
use Erp\NotificationBundle\Entity\History;
use Erp\NotificationBundle\Entity\Template;
use Twilio\Rest\Client;

class BaseNotificationCommand extends ContainerAwareCommand {

    const TYPE_NOTIFICATION = 'notification';
    const TYPE_ALERT = 'alert';
    const TYPES = [
        self::TYPE_NOTIFICATION,
        self::TYPE_ALERT,
    ];

    protected $prefix = '';

    /**
     * @inheritdoc
     */
    protected function configure() {
        $this
                ->setName('erp:notification');
    }

    /**
     * @inheritdoc
     */
    protected function process(string $type) {
        if (!in_array($type, self::TYPES)) {
            return -1;
        }

        $userNotificationEm = $this->getEntityManager(UserNotification::class);
        $propertyEm = $this->getEntityManager(Property::class);
        $templateManager = $this->getContainer()->get('erp_notification.template_manager');
        $producer = $this->getContainer()->get('old_sound_rabbit_mq.send_notification_producer');

        $method = null;
        if ($type === self::TYPE_NOTIFICATION) {
            $this->prefix = 'notification before';
            $method = 'getPropertiesFromNotificationsIterator';
        } elseif ($type === self::TYPE_ALERT) {
            $this->prefix = 'alert after';
            $method = 'getPropertiesFromAlertsIterator';
        }

        $i = 0;
        if ($iterableResult = $userNotificationEm->getRepository(UserNotification::class)->{$method}()) {
            foreach ($iterableResult as $propertyResult) {
                $data = reset($propertyResult);
                /** @var Property $property */
                if ($property = $propertyEm->getRepository(Property::class)->find($data['propertyId'])) {
                    $tenant = $property->getTenantUser();
                    try {
                        /** @var Template $template */
                        $template = $templateManager->getTemplate($data['templateId']);
                        $rendered = $templateManager->renderTemplate($template, [
                            'firstName' => $tenant->getFirstName(),
                            'lastName' => $tenant->getLastName(),
                            'date' => (new \DateTime())->format('d.m.Y'),
                            'daysBefore' => $data['calculatedDaysBefore'],
                            'daysAfter' => $data['calculatedDaysAfter'],
                        ]);
                    } catch (\Exception $ex) {
                        $this->logRenderError($ex, $data);
                        continue;
                    }

                    $manager = $property->getUser();
                    $managerFullName = sprintf('Manager %s', $manager->getFromForEmail());
                    $mailFrom = $this->getContainer()->getParameter('contact_email');

                    $msg = [
                        'mailTo' => $tenant->getEmail(),
                        'mailFrom' => $mailFrom,
                        'mailFromTitle' => $managerFullName,
                        'data' => $data,
                        'subject' => $manager->getCompanyName() ?: $managerFullName,
                        'rendered' => $rendered,
                        'tenantUser' => $tenant->getId(),
                        'property' => $property->getId(),
                        'prefix' => $this->prefix,
                    ];
                    $producer->publish(serialize($msg));

                    if ($data['sendSms'])
                        $msgSms = [
                            'subject' => $manager->getCompanyName() ?: $managerFullName,
                            'text' => $rendered
                        ];
                    $this->sendSms($msgSms);
                }

                $i++;
            }
        }
        return $i . ' emails should be sent (' . $this->prefix . ' payment date).';
    }

    private function sendSms($msgSms) {
        $sid = $this->getContainer()->getParameter('twilio_sid');
        $token = $this->getContainer()->getParameter('twilio_auth_token');
        $fromNumber = $this->getContainer()->getParameter('twilio_number');
        $twilio = new Client($sid, $token);
        try {
            $m = $twilio->messages->create(
                    $msgSms['toNumber'], array(
                "body" => $msg['subject'] . '-' . $msg['text'],
                "from" => $fromNumber,
                    )
            );
        } catch (\Exception $e) {
            $this->logRenderError($e, $msgSms);
        }
    }

    private function getEntityManager($class) {
        $repository = $this->getContainer()->get('doctrine')->getManagerForClass($class);

        return $repository;
    }

    private function logRenderError(\Exception $ex, $data) {
        $msg = '==============================' . "\n" .
                'Render error occured for Template (trying to send ' . $this->prefix . ' pay date).' . "\n" .
                'Data: ' . var_export($data, true) . "\n" .
                'Error message: ' . $ex->getMessage() . "\n" .
                '==============================';
        $this->getContainer()->get('erp_notification.logger')->error($msg);
    }

}
