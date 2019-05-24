<?php

namespace Erp\CoreBundle\EmailNotification;

interface EmailNotificationInterface
{
    public function sendEmailNotification($params);
}
