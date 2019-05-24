<?php

namespace Erp\CoreBundle\EmailNotification\Notifications;

use Erp\CoreBundle\EmailNotification\AbstractEmailNotification;
use Erp\CoreBundle\EmailNotification\EmailNotificationFactory;

class ManagerActiveInvite extends AbstractEmailNotification
{
    /**
     * @var string
     */
    protected $type = EmailNotificationFactory::TYPE_MANAGER_ACTIVE_INVITE;

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

        $subject = 'Zoobdoo - You have been invited as Manager';
        $template = 'ErpCoreBundle:EmailNotification:' . $this->type . '.html.twig';

        $emailParams['managerInvite'] = $params['managerInvite'];
        $emailParams['url'] = $params['url'];
        $emailParams['imageErp'] = $message->embed($this->getLogoPath($params));

        $message
            ->setSubject($subject)
            ->setBody($params['container']->get('templating')->render($template, $emailParams));
        $result = $params['mailer']->send($message);

        return $result;
    }
}
