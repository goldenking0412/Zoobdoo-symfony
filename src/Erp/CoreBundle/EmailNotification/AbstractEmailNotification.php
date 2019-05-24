<?php

namespace Erp\CoreBundle\EmailNotification;

abstract class AbstractEmailNotification implements EmailNotificationInterface
{
    protected $type;

    /**
     * Send email notification
     *
     */
    abstract public function sendEmailNotification($params);

    /**
     * Returns Email Notification type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    protected function getLogoPath($params)
    {
        $path = \Swift_Image::fromPath($params['basePath'] . '/bundles/erpcore/images/email_logo.png');

        return $path;
    }
}
