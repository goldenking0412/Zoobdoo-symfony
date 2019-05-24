<?php

namespace Erp\UserBundle\Twig;

use Erp\UserBundle\Entity\User;
use Erp\UserBundle\Entity\UserDocument;
use Erp\UserBundle\Entity\Message;
use Erp\UserBundle\Entity\ServiceRequest;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class UserExtension
 *
 * @package Erp\UserBundle\Twig
 */
class UserExtension extends \Twig_Extension {

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getManager();
    }

    /**
     * @return string
     */
    public function getName() {
        return 'user_extension';
    }

    /**
     * @return array
     */
    public function getFunctions() {
        return array(
            new \Twig_SimpleFunction('count_unread_messages', array($this, 'getCountUnreadMessages')),
            new \Twig_SimpleFunction('unread_messages', array($this, 'getUnreadMessages')),
            new \Twig_SimpleFunction('count_unread_service_requests', array($this, 'getCountUnreadServiceRequests')),
            new \Twig_SimpleFunction('get_pdf_link_signed', array($this, 'getPdfLinkOfSignedDocument')),
            new \Twig_SimpleFunction('isavailable_pdf_signed', array($this, 'isSignedPDFAvailable')),
            new \Twig_SimpleFunction('get_user_settings', array($this, 'getUserSettings')),
            new \Twig_SimpleFunction('mktime', 'mktime'),
            new \Twig_SimpleFunction('file_get_contents', 'file_get_contents'),
        );
    }

    public function getFilters() {
        return array(
            new \Twig_SimpleFilter('is_sender_signing', [$this, 'checkIsSenderSigning']),
        );
    }
    
    /**
     * 
     * @return type
     */
    public function getUserSettings() {
        return $this->container->get('erp.users.user.service')->getSettings();
    }
    
    /**
     * 
     * @param User $user
     * @return integer
     */
    public function getCountUnreadServiceRequests(User $user) {
        return $this->em->getRepository(ServiceRequest::REPOSITORY)->findUnreadServiceRequestsByUserTo($user);
    }

    /**
     * Return count unread messages
     *
     * @param User $user
     * @param User|null $fromUser
     *
     * @return int
     */
    public function getUnreadMessages(User $user, User $fromUser = null) {
        return $this->em->getRepository(Message::REPOSITORY)->getUnreadMessages($user, $fromUser);
    }

    /**
     * Return count unread messages
     *
     * @param User $user
     * @param User|null $fromUser
     *
     * @return int
     */
    public function getCountUnreadMessages(User $user, User $fromUser = null) {
        return $this->em->getRepository(Message::REPOSITORY)->getCountUnreadMessages($user, $fromUser);
    }

    /**
     * 
     * @param \Erp\UserBundle\Entity\UserDocument $userDocument
     * @return string
     */
    public function getPdfLinkOfSignedDocument(\Erp\UserBundle\Entity\UserDocument $userDocument) {
        $signatureId = $userDocument->getEnvelopIdToUser();
        return $this->container->get('erp.signature.hellosign.service')->getPdfLink($signatureId)->file_url;
    }

    /**
     * 
     * @param \Erp\UserBundle\Entity\UserDocument $userDocument
     * @return boolean
     */
    public function isSignedPDFAvailable(\Erp\UserBundle\Entity\UserDocument $userDocument) {
        $signatureId = $userDocument->getEnvelopIdToUser();

        return !(is_null($this->container->get('erp.signature.hellosign.service')->getPdfLink($signatureId)->file_url));
    }

    /**
     * 
     * @param UserDocument $userDocument
     * @param User $user
     * @return boolean
     */
    public function checkIsSenderSigning(UserDocument $userDocument, User $user) {
        return $this->container->get('erp.signature.hellosign.service')->isFromUserSigning($user, $userDocument);
    }

}
