<?php

namespace Erp\CoreBundle\EmailNotification\Notifications;

use Erp\CoreBundle\EmailNotification\AbstractEmailNotification;
use Erp\CoreBundle\EmailNotification\EmailNotificationFactory;

class AlertNotification extends AbstractEmailNotification
{
    /**
     * Send email notification when alert/notification after/before payment day occurred
     *
     * @param array $params
     */
    public function sendEmailNotification($params)
    {
        $message = $params['mailer']->createMessage()
            ->setFrom([$params['mailFrom'] => $params['mailFromTitle']])
            ->setTo($params['mailTo'])
            ->setContentType('text/html');

        $message->setSubject($params['subject'].' - Zoobdoo')->setBody($params['rendered']);
        
        $params['mailer']->getTransport()->start();
        $result = $params['mailer']->send($message);
        $params['mailer']->getTransport()->stop();

        return $result;
    }
}
