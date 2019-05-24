<?php

namespace Erp\CoreBundle\EmailNotification\Notifications;

use Erp\CoreBundle\EmailNotification\AbstractEmailNotification;
use Erp\CoreBundle\EmailNotification\EmailNotificationFactory;

class InviteTenantUser extends AbstractEmailNotification {

    /**
     * @var string
     */
    protected $type = EmailNotificationFactory::TYPE_INVITE_TENANT_USER;

    /**
     * Send email notification when new Administrator created
     *
     * @param array $params
     */
    public function sendEmailNotification($params) {
        /** @var $contailner \Symfony\Component\DependencyInjection\ContainerInterface */
        $contailner = $params['container'];

        $message = $params['mailer']->createMessage()
                ->setFrom([$params['mailFrom'] => $params['mailFromTitle']])
                ->setTo($params['sendTo'])
                ->setContentType('text/html');

        $template = 'ErpCoreBundle:EmailNotification:' . $this->getType() . '.html.twig';
        $emailParams = [
            'url' => $params['url'],
        ];

        $emailParams['imageErp'] = $message->embed($this->getLogoPath($params));

        $body = $contailner->get('templating')->render($template, $emailParams);
        $message->setSubject($params['preSubject'] . ' - Register your new tenant account now!')->setBody($body);
        $result = $params['mailer']->send($message);

        return $result;
    }

}
