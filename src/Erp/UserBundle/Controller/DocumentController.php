<?php

namespace Erp\UserBundle\Controller;

use Erp\CoreBundle\Entity\Document;
use Erp\PropertyBundle\Entity\Property;
use Erp\UserBundle\Entity\UserDocument;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Erp\UserBundle\Entity\User;
use Erp\UserBundle\Form\Type\UserDocumentFormType;
use Erp\CoreBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints\Regex as RegexConstraints;

/**
 * Class DocumentController
 *
 * @package Erp\UserBundle\Controller
 */
class DocumentController extends BaseController {

    /**
     * 
     * @param Request $request
     * @param type $toUserId
     * @return Response|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function indexAction(Request $request, $toUserId) {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if ($toUserId === null) {
            return $this->redirectToRoute('erp_user_documentation', ['toUserId' => UserDocument::APPLICANT_USER_ID]);
        }

        //TODO Handle if manager has selected tenant/landlord
        if (false) {
            throw $this->createNotFoundException();
        }

        $userRepository = $this->em->getRepository('ErpUserBundle:User');
        /** @var User $user */
        $user = $userRepository->find($toUserId);

        $anonUser = (new User())
                ->setId(0)
                ->setFirstName('Applicants')
                ->addRole(User::ROLE_ANONYMOUS);

        if (!$user) {
            $user = $anonUser;
        }

        $userDocument = new UserDocument();
        $form = $this->createUserDocumentForm($userDocument, $user);
        $form->handleRequest($request);

        $hellosignService = $this->get('erp.signature.hellosign.service');

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $fromUser = $user->hasRole(User::ROLE_ANONYMOUS) ? null : $currentUser;
                $toUser = $user->hasRole(User::ROLE_ANONYMOUS) ? $currentUser : $user;

                $userDocument
                        ->setFromUser($fromUser)
                        ->setToUser($toUser)
                ;
                $this->em->persist($userDocument);
                $this->em->flush();

                // save HelloSign signature info into the database
                $subjectEnvelop = 'The document to be signed';
                $messageEnvelop = 'Please, sign the current document.';
                try {
                    $response = $hellosignService->embedSignatureRequestFromDocument($userDocument, $subjectEnvelop, $messageEnvelop);
                    $signatures = $response->getSignatures();

                    $message = 'Document updoaded successfully.';

                    // send the alert email to landlord / tenant, if manager is not null (i.e., the user is a manager and not an applicant)
                    if (!(is_null($fromUser))) {
                        $userDocument->setEnvelopIdFromUser($hellosignService->getSignatureIdFromEmail($signatures, $fromUser->getEmail()));

                        if ($this->get('erp_user.mailer.processor')->sendSigningDocumentEmail($userDocument)) {
                            $message .= sprintf(' An email has been sent to %s with a link to sign the document.', $toUser->getEmail());
                        } else {
                            $message .= sprintf(' Unable to send the email %s for signing the requested document..', $toUser->getEmail());
                        }
                    } else {
                        $userDocument->setStatus(UserDocument::STATUS_PENDING);
                    }
                    $userDocument->setEnvelopIdToUser($hellosignService->getSignatureIdFromEmail($signatures, $toUser->getEmail()));

                    // flush again the new UserDocument
                    $this->em->flush();

                    $this->get('session')->getFlashBag()->add('alert_ok', $message);

                    return $this->redirectToRoute('erp_user_documentation', ['toUserId' => $toUserId]);
                } catch (\Exception $ex) {
                    $this->get('session')->getFlashBag()->add('alert_error', $ex->getMessage());
                }
            } else {
                $this->get('session')->getFlashBag()->add('alert_error', 'Uploading of document is failed.');
            }
        }

        $menu = $this->get('erp.site.left_menu_service')->createLeftMenu($currentUser, $anonUser);

        $userDocuments = $this->getUserDocuments($currentUser, $user);

        return $this->render('ErpUserBundle:Documentation:documentation.html.twig', [
                    'form' => $form->createView(),
                    'user' => $currentUser,
                    'user_documents' => $userDocuments,
                    'currentCompanion' => $user,
                    'menu' => $menu,
                    'role_tenant' => User::ROLE_TENANT,
                    'role_manager' => User::ROLE_MANAGER,
                    'role_anonymous' => User::ROLE_ANONYMOUS
        ]);
    }

    /**
     * Update user document
     *
     * @param Request $request
     * @param int     $documentId
     *
     * @return JsonResponse
     */
    public function updateAction(Request $request, $documentId) {
        $userDocumentName = $request->get('fileName');
        $userDocumentStatus = $request->get('fileStatus', null);

        /** @var UserDocument $userDocument */
        $userDocument = $this->getDoctrine()
                ->getRepository('ErpUserBundle:UserDocument')
                ->findOneBy(['id' => $documentId]);

        if (!$userDocument) {
            return new JsonResponse(['error' => 'File not found']);
        }

        $regexConstraints = new RegexConstraints(
                [
            'pattern' => Document::$patternFilename,
            'message' => Document::$messagePatternFilename
                ]
        );

        /** @var $errors \Symfony\Component\Validator\ConstraintViolationListInterface */
        $errors = $this->get('validator')->validateValue(
                $userDocumentName, $regexConstraints
        );

        if ($errors->count() || !strlen(trim($userDocumentName))) {
            return new JsonResponse(['errors' => 'The file name must not be empty and must contain an extension']);
        }

        $userDocument->getDocument()->setOriginalName($userDocumentName);
        if ($userDocumentStatus) {
            $userDocument->setStatus($userDocumentStatus);
        }

        $this->em->persist($userDocument);
        $this->em->flush();

        return new JsonResponse(['status' => true]);
    }

    /**
     * Delete user document
     *
     * @param Request $request
     * @param int $documentId
     *
     * @return JsonResponse
     */
    public function deleteAction(Request $request, $documentId) {
        /** @var User $user */
        $user = $this->getUser();
        /** @var UserDocument $userDocument */
        $userDocument = $this->em->getRepository('ErpUserBundle:UserDocument')->find($documentId);
        if (!$userDocument) {
            $renderOptions = [
                'askMsg' => 'File not found',
                'hideActionBtn' => true,
                'cancelBtn' => 'Ok'
            ];
        } elseif (!$user->hasRole(User::ROLE_MANAGER) && $userDocument->getFromUser()->getId() != $user->getId()) {
            $renderOptions = [
                'askMsg' => 'Not permissions',
                'hideActionBtn' => true,
                'cancelBtn' => 'Ok'
            ];
        } else {
            $renderOptions = [
                'askMsg' => 'Are you sure you want to delete document?',
                'actionBtn' => 'Delete',
                'actionUrl' => $this->generateUrl(
                        'erp_user_document_delete', ['documentId' => $documentId]
                ),
                'actionMethod' => 'DELETE'
            ];
        }

        if ($request->getMethod() === 'DELETE') {
            $this->em->remove($userDocument);
            $this->em->flush();

            return $this->redirect($request->headers->get('referer'));
        }

        return $this->render('ErpCoreBundle:crossBlocks:general-confirmation-popup.html.twig', $renderOptions);
    }

    /**
     * Create user document form
     *
     * @param UserDocument $userDocument
     * @param User $user
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function createUserDocumentForm(UserDocument $userDocument, User $user) {
        $action = $this->generateUrl('erp_user_documentation', ['toUserId' => $user->getId()]);

        $formOptions = ['action' => $action, 'method' => 'POST'];
        $form = $this->createForm(new UserDocumentFormType(), $userDocument, $formOptions);

        return $form;
    }

    /**
     * Return list user documents
     *
     * @param User $fromUser
     * @param User $toUser
     *
     * @return mixed
     */
    public function getUserDocuments(User $fromUser, User $toUser) {
        $userDocuments = $this->em->getRepository('ErpUserBundle:UserDocument')->getUserDocuments($fromUser, $toUser);

        $document = new Document();
        $uploadBaseDir = $document->getUploadBaseDir($this->container);

        /** @var UserDocument $userDocument */
        foreach ($userDocuments as $userDocument) {
            $filePath = $userDocument->getDocument()->getPath();
            $fileName = $userDocument->getDocument()->getName();
            $filePath = $uploadBaseDir . $filePath . '/' . $fileName;

            if (!file_exists($filePath)) {
                $this->em->remove($userDocument);
                $this->em->flush();
            }
        }

        return $userDocuments;
    }

    /**
     * Return list companions
     *
     * @param User $user
     *
     * @return array
     */
    public function getCompanions(User $user) {
        $companions = [];

        if ($user->hasRole(User::ROLE_MANAGER)) {
            // For anonymous area
            $companions[0] = (new User())
                    ->setId(0)
                    ->setFirstName('Applicants')
                    ->addRole(User::ROLE_ANONYMOUS)
            ;

            $properties = $user->getProperties();
            /** @var Property $property */
            foreach ($properties as $property) {
                if ($property->getTenantUser()) {
                    $companions[$property->getTenantUser()->getId()] = $property->getTenantUser();
                }
            }
        } else {
            if ($user->getTenantProperty()) {
                $companions[$user->getTenantProperty()->getUser()->getId()] = $user->getTenantProperty()->getUser();
            }
        }

        return $companions;
    }

}
