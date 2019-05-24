<?php

namespace Erp\CoreBundle\EmailNotification\Notifications;

use Erp\CoreBundle\EmailNotification\AbstractEmailNotification;
use Erp\CoreBundle\EmailNotification\EmailNotificationFactory;
use Erp\CoreBundle\Entity\EmailNotification;

class UserSetting extends AbstractEmailNotification
{
    /**
     * @var string
     */
    protected $type = EmailNotificationFactory::TYPE_USER_SETTING;

    /**
     * Send email notification
     *
     * @param array $params
     *
     * @return bool
     */
    public function sendEmailNotification($params)
    {
        $message = $params['mailer']
            ->createMessage()
            ->setFrom([$params['mailFrom'] => 'Zoobdoo'])
            ->setTo($params['sendTo'])
            ->setContentType("text/html");

        $emailParams['title'] = $params['title'];
        $emailParams['body'] = $params['body'];
        $emailParams['button'] = $params['button'];

        if (!empty($params['tokens']['#url#'])) {
            $emailParams['url'] = $params['tokens']['#url#'];
        }

        $emailParams['imageErp'] = $message->embed($this->getLogoPath($params));

        $template = 'ErpCoreBundle:EmailNotification:user_setting.html.twig';
        $message
            ->setSubject($params['subject'])
            ->setBody($params['container']->get('templating')->render($template, $emailParams));

        $result = $params['mailer']->send($message);

        return $result;
    }
}
