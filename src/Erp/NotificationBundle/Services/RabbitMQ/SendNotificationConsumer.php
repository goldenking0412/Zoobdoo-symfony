<?php

namespace Erp\NotificationBundle\Services\RabbitMQ;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use PhpAmqpLib\Message\AMQPMessage;
use Erp\CoreBundle\EmailNotification\EmailNotificationFactory;
use Erp\NotificationBundle\Entity\History;
use Erp\UserBundle\Entity\User;
use Erp\PropertyBundle\Entity\Property;

class SendNotificationConsumer implements ConsumerInterface
{
    private $container;

    public function setContainer(Container $container)
    {
        $this->container = $container;
        return $this;
    }

    public function execute(AMQPMessage $msg)
    {
        $data = unserialize($msg->body);
        if (!is_array($data)) {
            $this->logDataError($data);
            return;
        }

        $userEm = $this->container->get('doctrine')->getManagerForClass(User::class);
        $historyEm = $this->container->get('erp_notification.history_entity_manager');
        $mailer = $this->container->get('erp.core.email_notification.service');
        $historyManager = $this->container->get('erp_notification.history_manager');

        $emailParams = [
            'mailTo' => $data['mailTo'],
            'mailFrom' => $data['mailFrom'],
            'mailFromTitle' => $data['mailFromTitle'],
            'subject' => $data['subject'],
            'rendered' => $data['rendered'],
        ];
        try {
            if ($mailer->sendEmail(EmailNotificationFactory::TYPE_ALERT_NOTIFICATION, $emailParams)) {
                $fields = $data['data'];
                $fields['tenant'] = $userEm->getRepository(User::class)->find($data['tenantUser']);
                $fields['property'] = $userEm->getRepository(Property::class)->find($data['property']);
                
                $history = $historyManager->create($fields);

                $historyEm->persist($history);
                $historyEm->flush();

                $this->logSuccess($data, $data['prefix']);
            } else {
                $this->logEmailError($data, null, $data['prefix']);
            }
        } catch (\Exception $ex) {
            $this->logEmailError($data, $ex, $data['prefix']);
        }
    }

    private function logSuccess($data, $prefix = '~unknown~')
    {
        $msg =
            'Success '.$prefix.' pay date.'."\n".
            'Data: '.var_export($data, true)."\n";
        $this->container->get('erp_notification.logger')->info($msg);
    }

    private function logEmailError($data, \Exception $ex = null, $prefix = '~unknown~')
    {
        $msg = '=============================='."\n".
            'Cannot send an email (trying to send '.$prefix.' pay date).'."\n".
            'Data: '.var_export($data, true)."\n".
            ($ex ? $ex->getMessage()."\n" : '').
            '==============================';
        $this->container->get('erp_notification.logger')->error($msg);
    }

    private function logDataError($data, $prefix = '~unknown~')
    {
        $msg = '=============================='."\n".
            'Cannot parse data (trying to send '.$prefix.' pay date).'."\n".
            'Data: '.var_export($data, true)."\n".
            '==============================';
        $this->container->get('erp_notification.logger')->error($msg);
    }
}
