<?php

namespace Erp\SignatureBundle\Controller;

use Erp\CoreBundle\Controller\BaseController;
use Erp\UserBundle\Entity\UserDocument;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class SignatureController
 *
 * @package Erp\SignatureBundle\Controller
 */
class SignatureController extends BaseController {

    /**
     * This function is called by Ajax in order to retrieve the features of the
     * document to sign, and returns a json response with the variable useful
     * for showing the modal with embedded HelloSign API
     * 
     * @param integer $userDocumentId
     * @return Response|RedirectResponse
     * @throws NotFoundHttpException
     * @throws Exception
     */
    public function editEnvelopHelloSignAction($userDocumentId) {
        /** @var UserDocument $document */
        $userDocument = $this->em->getRepository(UserDocument::class)->find($userDocumentId);

        list($result, $message) = $this->checkUserDocument($userDocument);
        if ($result) {
            try {
                // check if the current user, who is signing, is a manager but not as applicant
                $myEnvelopId = $this->get('erp.signature.hellosign.service')->getEnvelopIdSigningUser($this->getUser(), $userDocument);
                $signUrl = $this->get('hellosign.client')->getEmbeddedSignUrl($myEnvelopId)->getSignUrl();

                $data = array(
                    'SIGN_URL' => $signUrl,
                    'CLIENT_ID' => $this->getParameter('hellosign_app_clientid'),
                    'TEST_ENV' => $this->getParameter('hellosign_app_testenv')
                );

                return new JsonResponse($data, Response::HTTP_OK);
            } catch (\Exception $ex) {
                return new JsonResponse(array('error' => $ex->getMessage()), $ex->getCode());
            }
        } else {
            return new JsonResponse(array('error' => $message), Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * This function is called after successful signing of the document, run by
     * an ajax request within ErpUserBundle\Resources\public\js\documentation.controller.js::helloSignGetDocSigned.
     * If HelloSign Javascript client picks up the signing of a document, it runs an Ajax request
     * to the current function.
     * This function keeps the status as is if the signing user is a manager/applicant,
     * otherwise sets the status as PENDING. In this latter situation (landlord/tenant is signing),
     * an email towards the manager is sent to alert of signature.
     * Then, numOfSignature of the UserDocument is increased by one, and compared with
     * UserDocument->getMaxNumOfSignatures(): if they are equal, the status is set
     * to COMPLETED and an email for downloading of signed PDF is sent to both users
     * 
     * @param Request $request
     * @param string $userDocumentId
     * @return JsonResponse
     */
    public function saveEnvelopAfterHelloSignAction(Request $request, $userDocumentId) {
        /** @var UserDocument $document */
        $userDocument = $this->em->getRepository(UserDocument::class)->find($userDocumentId);

        list($result, $message) = $this->checkUserDocument($userDocument);

        if ($result) {
            /** @var Erp\UserBundle\Mailer\Processor $mailerService */
            $mailerService = $this->get('erp_user.mailer.processor');

            /** @var Erp\SignatureBundle\Service\HelloSignService $helloSignService */
            $helloSignService = $this->get('erp.signature.hellosign.service');

            // it's a landlord / tenant
            if (!($helloSignService->isFromUserSigning($this->getUser(), $userDocument))) {
                $userDocument->setStatus(UserDocument::STATUS_PENDING);

                // try to send the email to the manager
                if ($mailerService->sendAcceptedDocumentEmail($userDocument)) {
                    $message .= sprintf(' An email has been sent to %s to alert that you signed the requested document.', $userDocument->getFromUser()->getEmail());
                } else {
                    $message .= sprintf(' Unable to send the email %s for alerting about your signature of the requested document.', $userDocument->getFromUser()->getEmail());
                }
            }

            // check if all the requested users have signed the document
            $numOfSignatures = $userDocument->getNumOfSignatures() + 1;
            $userDocument->setNumOfSignatures($numOfSignatures);

            // all the requested users have signed
            if ($numOfSignatures == $userDocument->getMaxNumOfSignatures()) {
                $userDocument->setStatus(UserDocument::STATUS_COMPLETED);

                // send the email with the link of signed PDF file which could be downloaded
                $result = $helloSignService->getPdfLink($userDocument->getEnvelopIdToUser());
                if ($mailerService->sendSignedDocumentEmail($userDocument, $result)) {
                    if ($userDocument->getFromUser()) {
                        $message .= sprintf(' An email has been sent to %s and %s with a link to download the signed PDF.', $userDocument->getFromUser()->getEmail(), $userDocument->getToUser()->getEmail());
                    } else {
                        $message .= sprintf(' An email has been sent to %s with a link to download the signed PDF.', $userDocument->getToUser()->getEmail());
                    }
                } else {
                    $message .= ' Unable to send the email for downloading the signed PDF. File could not be available yet.';
                }
            }

            $this->em->persist($userDocument);
            $this->em->flush();

            return new JsonResponse(array('message' => $message, 'status' => $userDocument->getStatus()), Response::HTTP_OK);
        } else {
            return new JsonResponse($message, Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Create a new HelloSign template, editing an uploaded document
     * 
     * @param integer $userDocumentId
     * @return JsonResponse
     */
    public function manageTemplateHelloSignAction($userDocumentId) {
        /** @var UserDocument $document */
        $userDocument = $this->em->getRepository(UserDocument::class)->find($userDocumentId);

        list($result, $message) = $this->checkUserDocument($userDocument);
        if ($result) {
            try {
                $response = $this->get('erp.signature.hellosign.service')->manageTemplateRequest($userDocument);

                if (is_null($userDocument->getHelloSignTemplate())) {
                    $userDocument->setHelloSignTemplate($response->getId());
                    $this->em->flush();
                }

                // $isEmbeddedDraft = $response->isEmbeddedDraft();

                $data = array(
                    'TEMPLATE_URL' => $response->getEditUrl(),
                    'CLIENT_ID' => $this->getParameter('hellosign_app_clientid'),
                    'TEST_ENV' => $this->getParameter('hellosign_app_testenv')
                );

                return new JsonResponse($data, Response::HTTP_OK);
            } catch (\Exception $ex) {
                return new JsonResponse(array('error' => $ex->getMessage()), $ex->getCode());
            }
        } else {
            return new JsonResponse(array('error' => $message), Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * 
     * @param integer $userDocumentId
     * @return JsonResponse
     */
    public function removeTemplateHelloSignAction($userDocumentId) {
        /** @var UserDocument $document */
        $userDocument = $this->em->getRepository(UserDocument::class)->find($userDocumentId);

        list($result, $message) = $this->checkUserDocument($userDocument);
        if ($result) {
            $this->get('erp.signature.hellosign.service')->deleteTemplate($userDocument);

            $templateId = $userDocument->getHelloSignTemplate();

            $userDocument->setHelloSignTemplate(null);
            $this->em->flush();

            return new JsonResponse(sprintf('Successfully removed templateId: %s', $templateId), Response::HTTP_OK);
        } else {
            return new JsonResponse(array('error' => $message), Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * 
     * @param UserDocument $userDocument
     * @return array
     */
    protected function checkUserDocument(UserDocument $userDocument) {
        $result = true;
        $message = '';

        if (!$userDocument || !$userDocument->getDocument()) {
            $result = false;
            $message = 'Document not found';
        }

        if ($userDocument->isSigned()) {
            $result = false;
            $message = 'Document already signed';
        }

        return array($result, $message);
    }

}
