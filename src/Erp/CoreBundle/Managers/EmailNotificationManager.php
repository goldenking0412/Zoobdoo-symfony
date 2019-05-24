<?php
namespace Erp\CoreBundle\Managers;

use Doctrine\Common\Persistence\ObjectManager;
use Erp\CoreBundle\Entity\EmailNotification;

class EmailNotificationManager
{
    private $om;

    function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    public function update(EmailNotification $emailNotification, $flush = false)
    {
        $this->om->persist($emailNotification);
        if ($flush) {
            $this->om->flush();
        }
    }

    public function delete(EmailNotification $emailNotification)
    {
        $this->om->remove($emailNotification);
        $this->om->flush();
    }

}