<?php

namespace Erp\CoreBundle\EmailNotification\Notifications;

use Erp\CoreBundle\EmailNotification\AbstractEmailNotification;
use Erp\CoreBundle\EmailNotification\EmailNotificationFactory;

class LandlordInvite extends AbstractEmailNotification {

    /**
     * @var string
     */
    protected $type = EmailNotificationFactory::TYPE_LANDLORD_INVITE;

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

        $subject = 'Zoobdoo - You have been invited to register as Landlord';
        $template = 'ErpCoreBundle:EmailNotification:' . $this->type . '.html.twig';

        $emailParams['managerInvite'] = $params['managerInvite'];
        $emailParams['managerEmail'] = $params['managerEmail'];
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
