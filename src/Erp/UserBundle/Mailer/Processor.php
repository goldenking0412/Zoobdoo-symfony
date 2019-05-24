<?php

namespace Erp\UserBundle\Mailer;

use Erp\UserBundle\Entity\Charge;
use Erp\UserBundle\Entity\User;
use Erp\UserBundle\Entity\UserDocument;

class Processor extends BaseProcessor {

    const CHARGE_EMAIL_TEMPLATE = 'ErpUserBundle:Landlords:charge_email_template.html.twig';
    const END_OF_TRIAL_PERIOD_TEMPLATE = 'ErpUserBundle:Landlords:end_of_trial_period_template.html.twig';
    const SIGNING_DOCUMENT_EMAIL_TEMPLATE = 'ErpUserBundle:Documentation:signing_document_email_template.html.twig';
    const ACCEPTED_DOCUMENT_EMAIL_TEMPLATE = 'ErpUserBundle:Documentation:accepted_document_email_template.html.twig';
    const SIGNED_DOCUMENT_EMAIL_TEMPLATE = 'ErpUserBundle:Documentation:signed_document_email_template.html.twig';
    const TRANSFER_EMAIL_TEMPLATE = 'ErpUserBundle:Landlords:transfer_email_template_1.html.twig';
    const VENDOR_CREATED_TEMPLATE = 'ErpVendorBundle:Mail:vendor_created_email.html.twig';

    protected $mailFrom;

    /**
     * 
     * @param \Swift_Mailer $mailer
     * @param \Erp\UserBundle\Mailer\EngineInterface $templating
     * @param type $mailFrom
     */
    public function __construct($mailFrom) {
        $this->mailFrom = $mailFrom;
    }

    /**
     * 
     * @param Charge $charge
     * @return boolean
     */
    public function sendChargeEmail(Charge $charge) {
        $receiver = $charge->getReceiver();

        $rendered = $this->templating->render(self::CHARGE_EMAIL_TEMPLATE, array('charge' => $charge));
        $subject = sprintf('[Zoobdoo] Charge request from %s to %s', $charge->getManager()->getFullName(), $receiver->getFullName());

        return $this->sendEmail($rendered, $subject, $this->mailFrom, $receiver->getEmail(), 'text/html');
    }

    /**
     * 
     * @param User $user
     * @return boolean
     */
    public function sendEndOfTrialPeriodEmail(User $user) {
        $rendered = $this->templating->render(self::END_OF_TRIAL_PERIOD_TEMPLATE, ['user' => $user]);
        $subject = sprintf('[Zoobdoo] Trial period expired');

        return $this->sendEmail($rendered, $subject, $this->mailFrom, $user->getEmail(), 'text/html');
    }

    /**
     * 
     * @param UserDocument $document
     * @return boolean
     */
    public function sendSigningDocumentEmail(UserDocument $document) {
        $rendered = $this->templating->render(self::SIGNING_DOCUMENT_EMAIL_TEMPLATE, array(
            'document' => $document
        ));
        $subject = sprintf('[Zoobdoo] Sign a document sent by %s', $document->getFromUser()->getFullNameWithEmail());

        return $this->sendEmail($rendered, $subject, $this->mailFrom, $document->getToUser()->getEmail(), 'text/html');
    }

    /**
     * 
     * @param UserDocument $document
     * @return boolean
     */
    public function sendAcceptedDocumentEmail(UserDocument $document) {
        $rendered = $this->templating->render(self::ACCEPTED_DOCUMENT_EMAIL_TEMPLATE, array(
            'document' => $document
        ));
        $subject = sprintf('[Zoobdoo] The landlord/tenant %s signed your document', $document->getToUser()->getFullNameWithEmail());

        return $this->sendEmail($rendered, $subject, $this->mailFrom, $document->getToUser()->getEmail(), 'text/html');
    }

    /**
     * 
     * @param UserDocument $document
     * @param \HelloSign\FileResponse $result
     * @return boolean
     */
    public function sendSignedDocumentEmail(UserDocument $document, \HelloSign\FileResponse $result) {
        $fileLink = $result->file_url;
        $expiration = (new \DateTime())->setTimestamp($result->expires_at);

        if ($fileLink) {
            $rendered = $this->templating->render(self::SIGNED_DOCUMENT_EMAIL_TEMPLATE, array(
                'pdf_link' => $fileLink,
                'expiration' => $expiration,
                'document' => $document
            ));
            $subject = sprintf('[Zoobdoo] Get the document signed by %s', $document->getToUser()->getFullNameWithEmail());

            return $this->sendEmail($rendered, $subject, $this->mailFrom, $document->getFromUser()->getEmail(), 'text/html', $document->getToUser()->getEmail());
        } else {
            return false;
        }
    }

    /**
     * 
     * @param Charge $charge
     * @return boolean
     */
    public function sendTransferEmail(Charge $charge) {
        $rendered = $this->templating->render(self::TRANSFER_EMAIL_TEMPLATE, ['charge' => $charge]);
        $subject = sprintf('Transfer from %s to %s', $charge->getManager()->getFullName(), $charge->getReceiver()->getFullName());
        
        return $this->sendEmail($rendered, $subject, $this->mailFrom, $charge->getReceiver()->getEmail(), 'text/html');
    }
    
    /**
     * 
     * @param \Erp\VendorBundle\Entity\VendorCreate $vendor
     * @param User $creator
     * @return boolean
     */
    public function sendVendorCreatedEmail(\Erp\VendorBundle\Entity\VendorCreate $vendor, User $creator) {
        $rendered = $this->templating->render(self::VENDOR_CREATED_TEMPLATE, ['vendor' => $vendor, 'creator' => $creator]);
        $subject = sprintf('You have been added as a Vendor to Zoobdoo from %s', $creator->getFullName());
        
        return $this->sendEmail($rendered, $subject, $this->mailFrom, $vendor->getEmail(), 'text/html');
    }

}
