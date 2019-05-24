<?php

namespace Erp\CoreBundle\EmailNotification\Notifications;

use Erp\CoreBundle\EmailNotification\AbstractEmailNotification;
use Erp\CoreBundle\EmailNotification\EmailNotificationFactory;

class ContactFormToAdmin extends AbstractEmailNotification
{
    /**
     * @var string
     */
    protected $type = EmailNotificationFactory::TYPE_CONTACT_FORM_TO_ADMIN;

    /**
     * Send email notification for admin about contact form
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

        $subject = 'Zoobdoo - Message from Contact Form';
        $template = 'ErpCoreBundle:EmailNotification:' . $this->type . '.html.twig';

        $emailParams['url'] = $params['url'];

        $emailParams['name'] = $params['formData']['name'];
        $emailParams['email'] = $params['formData']['email'];
        $emailParams['phone'] = $params['formData']['phone'];
        $emailParams['subject'] = $params['formData']['subject'];
        $emailParams['message'] = $params['formData']['message'];

        $emailParams['imageErp'] = $message->embed($this->getLogoPath($params));

        $message
            ->setSubject($subject)
            ->setBody($params['container']->get('templating')->render($template, $emailParams));
        $result = $params['mailer']->send($message);

        return $result;
    }
}
