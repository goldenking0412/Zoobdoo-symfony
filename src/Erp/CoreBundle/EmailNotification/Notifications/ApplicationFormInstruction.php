<?php

namespace Erp\CoreBundle\EmailNotification\Notifications;

use Erp\CoreBundle\EmailNotification\AbstractEmailNotification;
use Erp\CoreBundle\EmailNotification\EmailNotificationFactory;

class ApplicationFormInstruction extends AbstractEmailNotification
{
    /**
     * @var string
     */
    protected $type = EmailNotificationFactory::TYPE_APPLICATION_FORM_INSTRUCTION;

    /**
     * Send email notification when new Administrator created
     *
     * @param array $params
     */
    public function sendEmailNotification($params)
    {
        /** @var $contailner \Symfony\Component\DependencyInjection\ContainerInterface */
        $contailner = $params['container'];
        $eImg = $this->getLogoPath($params);

        $message = $params['mailer']->createMessage()
            ->setFrom([$params['mailFrom'] => 'Zoobdoo'])
            ->setTo($params['sendTo'])
            ->setContentType('text/html');

        $template = 'ErpCoreBundle:EmailNotification:' . $this->getType() . '.html.twig';
        $emailParams = [
            'imageErp'  => $message->embed($eImg),
            'llEmail' => $params['llEmail'],
            'llName' => $params['llName']
        ];

        $body = $contailner->get('templating')->render($template, $emailParams);
        $message->setSubject('Zoobdoo - Online Application e-Sign Instruction')->setBody($body);
        $result = $params['mailer']->send($message);

        return $result;
    }
}
