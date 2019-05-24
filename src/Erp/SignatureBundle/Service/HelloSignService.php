<?php

namespace Erp\SignatureBundle\Service;

use Erp\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Erp\UserBundle\Entity\UserDocument;

class HelloSignService {

    const HELLOSIGN_STATUS_SENT = 'sent';
    const HELLOSIGN_STATUS_CREATED = 'created';

    /**
     * @var ContainerInterface
     */
    protected $container;
    protected $client;

    /**
     * Construct method
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->client = $container->get('hellosign.client');
    }

    /**
     * 
     * Sender could be null if the manager is uploading a new document to be signed
     * as an applicant
     * 
     * @param UserDocument $userDocument
     * @param string $subject
     * @param string $message
     * @return \HelloSign\EmbeddedSignatureRequest
     * @throws \Exception
     */
    public function embedSignatureRequestFromDocument(UserDocument $userDocument, $subject = '', $message = '') {
        try {
            $sender = $userDocument->getFromUser();
            $recipient = $userDocument->getToUser();
            
            if ($userDocument->hasTemplate()) { // subject, message and file have been already added when template has been prepared
                $request = new \HelloSign\TemplateSignatureRequest;
                $request->setTemplateId($userDocument->getHelloSignTemplate());
                
                // add signers
                if ($sender) {
                    $request->setSigner('Sender', $sender->getEmail(), $sender->getFullName());
                }
                $request->setSigner('Recipient', $recipient->getEmail(), $recipient->getFullName());
            } else { // otherwise add all fields / info: subject, message, file and signers
                $request = new \HelloSign\SignatureRequest;
                
                // add file, subject and message
                $document = $userDocument->getDocument();
                $request
                        ->addFile($document->getUploadBaseDir($this->container) . $document->getPath() . '/' . $document->getName())
                        ->setSubject($subject)
                        ->setMessage($message)
                ;

                // add signers
                if ($sender) {
                    $request->addSigner($sender->getEmail(), $sender->getFullName());
                }
                $request->addSigner($recipient->getEmail(), $recipient->getFullName());
            }


            if ($this->container->getParameter('hellosign_app_testenv')) {
                $request->enableTestMode();
            }

            $embeddedRequest = new \HelloSign\EmbeddedSignatureRequest($request, $this->container->getParameter('hellosign_app_clientid'));
            $response = $this->client->createEmbeddedSignatureRequest($embeddedRequest);

            return $response;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 
     * @param string $signatureId
     * @return \HelloSign\FileResponse
     */
    public function getPdfLink($signatureId) {
        return $this->client->getFiles($signatureId, null, \HelloSign\SignatureRequest::FILE_TYPE_PDF);
    }

    /**
     * 
     * @param array $signatures
     * @param string $email
     * @return string | null
     */
    public function getSignatureIdFromEmail($signatures, $email) {
        $found = false;
        $id = null;
        for ($i = 0; $i < count($signatures) && !($found); $i++) {
            if ($signatures[$i]->getSignerEmail() == $email) {
                $found = true;
                $id = $signatures[$i]->getId();
            }
        }

        return $id;
    }

    /**
     * 
     * @param User $user
     * @param UserDocument $userDocument
     * @return boolean
     */
    public function isFromUserSigning(User $user, UserDocument $userDocument) {
        $manager = is_null($userDocument->getFromUser()) ? $userDocument->getToUser() : $userDocument->getFromUser();
        return ($user->getId() == $manager->getId());
    }

    /**
     * 
     * @param User $user
     * @param UserDocument $userDocument
     * @return string
     */
    public function getEnvelopIdSigningUser(User $user, UserDocument $userDocument) {
        if (is_null($userDocument->getFromUser())) { // always get the envelopIdToUser
            return $userDocument->getEnvelopIdToUser();
        } else {
            return ($this->isFromUserSigning($user, $userDocument)) ? $userDocument->getEnvelopIdFromUser() : $userDocument->getEnvelopIdToUser();
        }
    }
    
    /**
     * 
     * @param UserDocument $userDocument
     * @param string $title
     * @param string $subject
     * @param string $message
     * @return \HelloSign\Template
     * @throws \Exception
     */
    public function manageTemplateRequest(UserDocument $userDocument, $title = '', $subject = '', $message = '') {
        try {
            $templateId = $userDocument->getHelloSignTemplate();
            
            if (is_null($templateId)) {
                $request = new \HelloSign\Template();
                if ($this->container->getParameter('hellosign_app_testenv')) {
                    $request->enableTestMode();
                }

                $request->setClientId($this->container->getParameter('hellosign_app_clientid'));

                $document = $userDocument->getDocument();
                $request
                        ->addFile($document->getUploadBaseDir($this->container) . $document->getPath() . '/' . $document->getName())
                        ->setTitle($title)
                        ->setSubject($subject)
                        ->setMessage($message)
                ;

                $i = 1;
                if (!(is_null($userDocument->getFromUser()))) {
                    $request->addSignerRole('Sender', $i);
                    $i++;
                }

                $request->addSignerRole('Recipient', $i);
                // $request->addMergeField('Click to sign (addresseer)', 'text');
                // $request->addMergeField('Click to sign (addressee)', 'text');

                $response = $this->client->createEmbeddedDraft($request);

                return $response;
            } else {
                return $this->client->getEmbeddedEditUrl($templateId);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
    
    /**
     * 
     * @param UserDocument $userDocument
     * @return boolean
     * @throws \Exception
     */
    public function deleteTemplate(UserDocument $userDocument) {
        try {
            if (!(is_null($userDocument->getHelloSignTemplate()))) {
                $this->client->deleteTemplate($userDocument->getHelloSignTemplate());
            }

            return true;
        } catch (\Exception $ex) {
            return false;
        }
    }

}
            
