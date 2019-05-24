<?php

namespace Erp\CoreBundle\EmailNotification\Notifications;

use Erp\CoreBundle\EmailNotification\AbstractEmailNotification;
use Erp\CoreBundle\EmailNotification\EmailNotificationFactory;

class ManagerInviteLandlord extends AbstractEmailNotification
{
    /**
     * @var string
     */
    protected $type = EmailNotificationFactory::TYPE_MANAGER_INVITE_LANDLORD;

    /**
     * Send email notification when new Administrator created
     *
     * @param array $params
     */
    public function sendEmailNotification($params) {
        $mailer = $params['mailer'];
        
        $message = $mailer
                ->createMessage()
                ->setFrom([$params['mailFrom'] => 'Zoobdoo'])
                ->setTo($params['sendTo'])
                ->setContentType("text/html");

        $subject = 'Zoobdoo - You have been invited to register as MAnager';
        $template = 'ErpCoreBundle:EmailNotification:' . $this->type . '.html.twig';

        $emailParams['landlordInvite'] = $params['landlordInvite'];
        $emailParams['landlordEmail'] = $params['landlordEmail'];
        $emailParams['message'] = array_key_exists('message', $params) ? $params['message'] : '';
        $emailParams['url'] = $params['url'];
        $emailParams['imageErp'] = $message->embed($this->getLogoPath($params));

        $message
                ->setSubject($subject)
                ->setBody($params['container']->get('templating')->render($template, $emailParams));
        $result = $mailer->send($message);

        return $result;
    }
}
