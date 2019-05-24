<?php

namespace Erp\CoreBundle\EmailNotification\Notifications;

use Erp\CoreBundle\EmailNotification\AbstractEmailNotification;
use Erp\CoreBundle\EmailNotification\EmailNotificationFactory;

class UserDeactivate extends AbstractEmailNotification {

    /**
     * @var string
     */
    protected $type = EmailNotificationFactory::TYPE_USER_DEACTIVATE;

    /**
     * Send email notification when new Administrator created
     *
     * @param array $params
     */
    public function sendEmailNotification($params) {
        $message = $params['mailer']
                ->createMessage()
                ->setFrom([$params['mailFrom'] => $params['mailFromTitle']])
                ->setTo($params['sendTo'])
                ->setContentType("text/html");

        $subject = $params['preSubject'] . ' - Your account has been deactivated';
        $template = 'ErpCoreBundle:EmailNotification:' . $this->type . '.html.twig';

        $emailParams['url'] = $params['url'];
        $emailParams['imageErp'] = $message->embed($this->getLogoPath($params));

        $message
                ->setSubject($subject)
                ->setBody($params['container']->get('templating')->render($template, $emailParams));
        $result = $params['mailer']->send($message);

        return $result;
    }

}
