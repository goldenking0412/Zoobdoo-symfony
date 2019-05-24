<?php

namespace Erp\CoreBundle\EmailNotification\Notifications;

use Erp\CoreBundle\EmailNotification\AbstractEmailNotification;
use Erp\CoreBundle\EmailNotification\EmailNotificationFactory;

class AppointmentRequest extends AbstractEmailNotification
{
    /**
     * @var string
     */
    protected $type = EmailNotificationFactory::TYPE_APPOINTMENT_REQUEST;

    /**
     * Send email notification when new Administrator created
     *
     * @param array $params
     */
    public function sendEmailNotification($params)
    {
        $message = $params['mailer']
            ->createMessage()
            ->setFrom([$params['mailFrom'] => 'Zoobdoo'])
            ->setTo($params['sendTo'])
            ->setContentType("text/html");

        $subject = 'Zoobdoo - You have received an appointment request';
        $template = 'ErpCoreBundle:EmailNotification:' . $this->type . '.html.twig';

        $appointmentRequest = $params['appointmentRequest'];

        $emailParams = array();

        $emailParams['userName'] = $appointmentRequest->getUserName();
        $emailParams['userEmail'] = $appointmentRequest->getEmail();
        $emailParams['subject'] = $appointmentRequest->getSubject();
        $emailParams['message'] = $appointmentRequest->getMessage();
        $emailParams['url'] = $params['url'];

        $emailParams['imageErp'] = $message->embed($this->getLogoPath($params));

        $message
            ->setSubject($subject)
            ->setBody($params['container']->get('templating')->render($template, $emailParams));
        $result = $params['mailer']->send($message);

        return $result;
    }
}
