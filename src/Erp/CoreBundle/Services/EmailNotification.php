<?php
namespace Erp\CoreBundle\Services;

use Erp\CoreBundle\EmailNotification\EmailNotificationFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

class EmailNotification
{
    protected $container;
    protected $mailFrom;
    protected $basePath;
    protected $baseUrl;
    protected $mailer;

    /**
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->mailFrom = $this->container->getParameter('contact_email');
        $this->basePath = $this->container->getParameter('assetic.write_to');
        $this->baseUrl = $this->container->getParameter('siteUrl');
        $this->mailer = $this->container->get('mailer');
    }

    /**
     * Send email notification
     *
     * @param $type
     * @param $params
     * @return mixed
     */
    public function sendEmail($type, $params)
    {
        $emailNotification = EmailNotificationFactory::getProvider($type);

        $params['container'] = $this->container;
        $params['mailFrom'] = $this->mailFrom;
        $params['mailFromTitle'] = isset($params['mailFromTitle']) ? $params['mailFromTitle'] : 'Zoobdoo';
        $params['preSubject'] = isset($params['preSubject']) ? $params['preSubject'] : 'Zoobdoo';
        $params['basePath'] = $this->basePath;
        $params['baseUrl'] = $this->baseUrl;
        $params['mailer'] = $this->mailer;

        return $emailNotification->sendEmailNotification($params);
    }
}
