<?php

namespace Erp\PropertyBundle\Controller;

use Erp\CoreBundle\Controller\BaseController;
use Erp\CoreBundle\Entity\Document;
use Erp\CoreBundle\Entity\Image;
use Erp\PropertyBundle\Entity\Property;
use Erp\PropertyBundle\Entity\PropertyRepostRequest;
use Erp\PropertyBundle\Form\Type\EditDocumentPropertyFormType;
use Erp\PropertyBundle\Form\Type\PropertyImportFormType;
use Erp\PropertyBundle\Form\Type\PropertySearchBarFormType;
use Erp\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Goodby\CSV\Export\Standard\ExporterConfig;
use Goodby\CSV\Export\Standard\Exporter;
use Goodby\CSV\Export\Standard\CsvFileObject;
use Goodby\CSV\Export\Standard\Collection\PdoCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Erp\StripeBundle\Form\Type\TransactionFilterType;
use Erp\PropertyBundle\Entity\PropertySecurityDeposit;

class ListingController extends BaseController {

    /**
     * Available properties page
     * 
     * @Security("is_granted('ROLE_MANAGER') or is_granted('ROLE_LANDLORD')")
     *
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request) {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->getUser();
        $filters = [];

        $form = $this->createPropertyImportFormType();
        $searchBarForm = $this->createSearchBarPropertyFormType();

        $searchBarForm->handleRequest($request);
        if ($searchBarForm->isValid()) {
            $filters = $searchBarForm->getData();
            /*
              I need a search bar, and when the manager types in something, like a tenant name, landlord name, unit name, or address, zip code, street, state, then whatever properties are matching. That is all.
             */
            $properties = $this->em->getRepository(Property::REPOSITORY)->findBySearchBar($user, $filters);
        } else {
            if ($user->hasRole(User::ROLE_MANAGER)) {
                $properties = $user->getProperties()->filter(function ($property) {
                    if ($property->getStatus() == Property::STATUS_DELETED) {
                        $return = false;
                    } else {
                        $return = true;
                    }

                    return $return;
                });
            } else {
                $properties = $this->em->getRepository(Property::REPOSITORY)->findAvailablePropertiesByLandlord($user);
            }
        }

        $currentPage = $request->query->getInt('page', 1);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $properties, $currentPage, Property::LIMIT_USER_LISTINGS
        );

        $prevPage = ceil($pagination->getTotalItemCount() / Property::LIMIT_USER_LISTINGS);
        if ($prevPage && ($currentPage > $prevPage)) {
            return $this->redirectToRoute('erp_property_listings_all', ['page' => $prevPage]);
        }

        $propertyFee = $this->get('erp.core.fee.service')->getPropertyFee();

        return $this->render('ErpPropertyBundle:Listings:index.html.twig', [
                    'user' => $user,
                    'pagination' => $pagination,
                    'form' => $form->createView(),
                    'propertyFee' => $propertyFee,
                    'searchBarForm' => $searchBarForm->createView(),
                    'propertyStatusRented' => Property::STATUS_RENTED,
        ]);
    }

    /**
     * Show Property page
     *
     * @Security("is_granted('ROLE_MANAGER') or is_granted('ROLE_LANDLORD')")
     * 
     * @param Request $request
     * @param int|null $propertyId
     * @return RedirectResponse|Response|NotFoundHttpException
     */
    public function showAction(Request $request, $propertyId) {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->getUser();

        if ($propertyId) {
            $property = $this->em->getRepository(Property::REPOSITORY)->getPropertyByUser($user, $propertyId);

            if (!$property) {
                throw $this->createNotFoundException();
            }
        } else {
            if ($user->getPropertyCounter() > 0 || $user->getIsPropertyCounterFree()) {
                $property = new Property();
                
                if ($user->hasRole(User::ROLE_MANAGER)) {
                    $property->setUser($user);
                } elseif ($user->hasRole(User::ROLE_LANDLORD)) {
                    $property->setLandlordUser($user);
                }
                
            } else {
                throw $this->createNotFoundException();
            }
        }

        $pageNumber = $request->get('page', 1);

        return $this->render('ErpPropertyBundle:Listings:show.html.twig', [
                    'user' => $user,
                    'property' => $property,
                    'page' => $pageNumber,
        ]);
    }

    /**
     * Manage listing documents action
     *
     * @Security("is_granted('ROLE_MANAGER') or is_granted('ROLE_LANDLORD')")
     * 
     * @param Request $request
     * @param int $propertyId
     * @return Response
     */
    public function editDocumentsAction(Request $request, $propertyId) {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->getUser();

        $property = $this->em->getRepository(Property::REPOSITORY)->getPropertyByUser($user, $propertyId);

        if (!$property) {
            throw $this->createNotFoundException();
        }

        $pageNumber = $request->get('page', 1);

        $form = $this->createEditDocumentPropertyForm($property, $pageNumber);

        if ($request->getMethod() == 'POST') {
            $preValidate = $this->preValidateFiles(
                    $request, $property, $form->getName(), ['documents', 'file']
            );

            $request = $preValidate['request'];
            $errors = $preValidate['errors'];

            $form->handleRequest($request);

            if ($errors) {
                $text = str_replace(
                        ['{maxSize}', '{sizeIn}'], [Document::$maxSize / 1024 / 1024, Document::SIZE_IN_MB], Document::$commonMessage
                );

                $this->addFlash('alert_error', $text);
            } else {
                $this->em->persist($property);
                $this->em->flush();
            }

            return $this->redirectToRoute(
                            'erp_property_listings_edit_documents', ['propertyId' => $property->getId(), 'page' => $pageNumber]
            );
        }

        return $this->render(
                        'ErpPropertyBundle:Listings:edit-documents.html.twig', ['user' => $user, 'form' => $form->createView(), 'property' => $property, 'page' => $pageNumber]
        );
    }

    /**
     * Delete property
     *
     * @Security("is_granted('ROLE_MANAGER') or is_granted('ROLE_LANDLORD')")
     * 
     * @param Request $request
     * @param $propertyId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function deleteAction(Request $request, $propertyId) {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->getUser();

        if ($user->isReadOnlyUser()) {
            throw $this->createNotFoundException();
        }

        /** @var Property $property */
        $property = $this->em->getRepository(Property::REPOSITORY)
                ->findOneBy(['id' => $propertyId, 'user' => $user]);

        if (!$property) {
            throw $this->createNotFoundException();
        }

        if ($property->getTenantUser() instanceof User || $property->getInvitedUsers()[0]) {
            $response = $this->render('ErpCoreBundle:crossBlocks:general-confirmation-popup.html.twig', [
                'askMsg' => 'Please, remove tenant',
                'hideActionBtn' => true,
                'cancelBtn' => 'OK',
            ]);
        } elseif ($request->getMethod() === 'DELETE') {
            if (!$user->isReadOnlyUser()) {
                $this->em->persist($user->setPropertyCounter($user->getPropertyCounter() + 1));
            }

            $deletedDate = new \DateTime('now');
            $property->setName(
                    $property->getName() . '[DELETED-' . $deletedDate->format('m/d/Y H:i:s') . ']'
            );
            $property->setStatus(Property::STATUS_DELETED);
            $this->em->flush();

            return $this->redirect($request->headers->get('referer'));
        } else {
            $response = $this->render('ErpCoreBundle:crossBlocks:delete-confirmation-popup.html.twig', [
                'askMsg' => 'Are you sure you want to delete this property?',
                'actionUrl' => $this->generateUrl(
                        'erp_property_listings_delete', ['propertyId' => $property->getId()]
                ),
                'actionMethod' => 'DELETE'
            ]);
        }

        return $response;
    }

    /**
     * Remove tenant from property
     *
     * @Security("is_granted('ROLE_MANAGER') or is_granted('ROLE_LANDLORD')")
     * 
     * @param Request $request
     * @param int $propertyId
     * @return JsonResponse
     */
    public function deleteTenantAction(Request $request, $propertyId) {
        if ($this->getUser()->isReadOnlyUser()) {
            throw $this->createNotFoundException();
        }
        /** @var $property \Erp\PropertyBundle\Entity\Property */
        $property = $this->em->getRepository(Property::REPOSITORY)->find($propertyId);
        $tenant = $property->getTenantUser();

        $askMsg = 'Are you sure you want to delete this tenant?';
        if (!$property || $property->getUser()->getId() !== $this->getUser()->getId()) {
            $askMsg = '404 Not Found';
        }

        if ($request->getMethod() === 'DELETE') {
            $userService = $this->get('erp.users.user.service');
            $userService->deactivateUser($tenant, true, $this->getUser());
            $userService->setStatusUnreadMessages($tenant);

            $property->setTenantUser(null)->setStatus(Property::STATUS_DRAFT);
            $this->em->persist($property);

            $invitedUser = $property->getInvitedUsers()->first();
            if ($invitedUser) {
                $this->em->remove($invitedUser);
            }

            $this->em->flush();

            return $this->redirect($request->headers->get('referer'));
        }

        return $this->render('ErpCoreBundle:crossBlocks:delete-confirmation-popup.html.twig', [
                    'askMsg' => $askMsg,
                    'actionUrl' => $this->generateUrl('erp_property_listings_delete_tenant', ['propertyId' => $propertyId])
        ]);
    }

    /**
     * Remove tenant with status pending
     * 
     * @Security("is_granted('ROLE_MANAGER') or is_granted('ROLE_LANDLORD')")
     *
     * @param Request $request
     * @param int $propertyId
     * @param int $invitedTenantId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function deleteInvitedTenantAction(Request $request, $propertyId, $invitedTenantId) {
        /** @var User $user */
        $user = $this->getUser();

        $askMsg = 'Are you sure you want to delete this invited tenant?';

        $keyUser = $user->hasRole(User::ROLE_MANAGER) ? 'user' : 'landlordUser';
        
        /** @var Property $property */
        $property = $this->em->getRepository(Property::REPOSITORY)
                ->findOneBy(['id' => $propertyId, $keyUser => $user]);
        if (!$property) {
            throw new NotFoundHttpException('No permissions');
        }

        if ($request->getMethod() === 'DELETE') {
            $invitedUser = $this->em->getRepository('ErpUserBundle:InvitedUser')
                    ->findOneBy(array('property' => $property, 'id' => $invitedTenantId));

            if ($invitedUser) {
                $this->em->remove($invitedUser);
            }

            if ($property->getInvitedUsers()->count() == 0) {
                $property->setStatus(Property::STATUS_DRAFT);
            }
            
            $this->em->flush();

            return $this->redirect($request->headers->get('referer'));
        }

        return $this->render('ErpCoreBundle:crossBlocks:delete-confirmation-popup.html.twig', array(
                    'askMsg' => $askMsg,
                    'actionUrl' =>
                    $this->generateUrl('erp_property_listings_delete_invited_tenant', array('propertyId' => $propertyId, 'invitedTenantId' => $invitedTenantId))
        ));
    }

    /**
     * Send repost property request
     * 
     * @Security("is_granted('ROLE_MANAGER') or is_granted('ROLE_LANDLORD')")
     *
     * @param Request $request
     * @param int $propertyId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function repostRequestAction(Request $request, $propertyId) {
        /** @var $user User */
        $user = $this->getUser();
        if ($user->isReadOnlyUser()) {
            throw $this->createNotFoundException();
        }

        /** @var $property Property */
        $property = $this->em->getRepository(Property::REPOSITORY)->find($propertyId);

        if (!$property) {
            throw new NotFoundHttpException();
        }

        if ($request->getMethod() === 'POST') {
            $repostRequest = new PropertyRepostRequest();
            $repostRequest->setProperty($property);
            $repostRequest->setStatus($repostRequest::STATUS_NEW);
            $repostRequest->setNote('');

            $this->em->persist($repostRequest);
            $this->em->flush();

            $this->addFlash('alert_ok', 'Success');

            return $this->redirect($request->headers->get('referer'));
        }

        throw new NotFoundHttpException();
    }

    /**
     * 
     * @param Request $request
     * @return type
     */
    public function searchAction(Request $request) {
        /** @var User $user */
        $user = $this->getUser();
        //TODO Do more flexible. Create filter model, form
        $type = $request->query->get('filter[type]', null, true);
        $interval = $request->query->get('filter[interval]', null, true);

        $propertyRepository = $this->em->getRepository(Property::class);

        $propertiesQuery = $propertyRepository->getPropertiesQuery($user, explode('-', $interval), explode(',', $type));

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $propertiesQuery, $request->query->getInt('page', 1), 10
        );

        return $this->render('ErpPropertyBundle:Listings:search-result.html.twig', [
                    'user' => $user,
                    'pagination' => $pagination,
        ]);
    }

    /**
     * @Security("is_granted('ROLE_MANAGER') or is_granted('ROLE_LANDLORD')")
     * 
     * @param Request $request
     * @return StreamedResponse
     */
    public function getAllPropertyAction(Request $request) {
        $user = $this->getUser();
        $uID = $user->getId();
        $type = $request->get('type');
        $conn = $this->get('database_connection');

        /** check type status for delete and active post */
        if ($type == '1') {
            $stmt = $stmt = $conn->prepare('SELECT properties.id,properties.name,properties.zip,properties.address,properties.about_properties,properties.additional_details,properties.amenities,properties.of_baths,properties.of_beds,properties.square_footage,cities.name as city_name, cities.country, cities.latitude, cities.longitude,property_repost_requests.note,property_repost_requests.status as repost_request_status FROM properties inner join cities on cities.id = properties.city_id inner join property_repost_requests on property_repost_requests.property_id = properties.id where property_repost_requests.status = "rejected" and DATE(property_repost_requests.updated_date) = CURRENT_DATE() and properties.user_id = "' . $uID . '"');
            $stmt->execute();
        } else {
            $stmt = $conn->prepare('SELECT properties.id,properties.name,properties.address,properties.zip,properties.about_properties,properties.additional_details,properties.amenities,properties.of_baths,properties.of_beds,properties.square_footage,cities.name as city_name, cities.country, cities.latitude, cities.longitude,property_repost_requests.note,property_repost_requests.status as repost_status FROM properties inner join cities on cities.id = properties.city_id inner join property_repost_requests on property_repost_requests.property_id = properties.id where property_repost_requests.status = "new" and DATE(properties.updated_date) = CURRENT_DATE() and properties.user_id = "' . $uID . '"');
            $stmt->execute();
        }

        /** create new stream response object */
        $response = new StreamedResponse();
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment');

        /** check type status for delete and active post */
        if ($type != '') {
            $response->headers->set('filename', 'deleted-post-data.csv');
        } else {
            $response->headers->set('filename', 'active-post-data.csv');
        }

        /** response callback function */
        $response->setCallback(function() use($stmt) {

            $results = $stmt->fetch();
            if ($results) {
                $keyArray = array();
                foreach ($results as $key => $val) {
                    $keyArray[] = $key;
                }

                $config = new ExporterConfig();
                $config
                        ->setDelimiter("\t") // Customize delimiter. Default value is comma(,)
                        ->setEnclosure("'")  // Customize enclosure. Default value is double quotation(")
                        ->setEscape("\\")    // Customize escape character. Default value is backslash(\)
                        ->setToCharset('SJIS-win') // Customize file encoding. Default value is null, no converting.
                        ->setFromCharset('UTF-8') // Customize source encoding. Default value is null.
                        ->setFileMode(CsvFileObject::FILE_MODE_WRITE) // Customize file mode and choose either write or append. Default value is write ('w'). See fopen() php docs
                        ->setColumnHeaders($keyArray)
                ;
                $exporter = new Exporter($config);

                $exporter->export('php://output', new PdoCollection($stmt->getIterator()), 'w');
            } else {
                $config = new ExporterConfig();
                $config
                        ->setDelimiter("\t") // Customize delimiter. Default value is comma(,)
                        ->setEnclosure("'")  // Customize enclosure. Default value is double quotation(")
                        ->setEscape("\\")    // Customize escape character. Default value is backslash(\)
                        ->setToCharset('SJIS-win') // Customize file encoding. Default value is null, no converting.
                        ->setFromCharset('UTF-8') // Customize source encoding. Default value is null.
                        ->setFileMode(CsvFileObject::FILE_MODE_WRITE) // Customize file mode and choose either write or append. Default value is write ('w'). See fopen() php docs
                ;
                $exporter = new Exporter($config);

                $exporter->export('php://output', new PdoCollection($stmt->getIterator()), 'w');
            }
        });
        $response->send();

        return $response;
    }

    /**
     * 
     * @Security("is_granted('ROLE_MANAGER') or is_granted('ROLE_LANDLORD')")
     * 
     * @param Request $request
     * @return Response
     */
    public function getPropertyXmlAction(Request $request) {
        $user = $this->getUser();
        $uID = $user->getId();
        $type = $request->get('type');
        $conn = $this->get('database_connection');

        /** check type status for delete and active post */
        if ($type == '1') {
            $stmt = $conn->prepare('SELECT properties.id,properties.name,properties.zip,properties.address,properties.about_properties,properties.additional_details,properties.amenities,properties.of_baths,properties.of_beds,properties.square_footage,cities.name as city_name, cities.country, cities.latitude, cities.longitude,property_repost_requests.note,property_repost_requests.status as repost_request_status FROM properties inner join cities on cities.id = properties.city_id inner join property_repost_requests on property_repost_requests.property_id = properties.id where property_repost_requests.status = "rejected" and DATE(property_repost_requests.updated_date) = CURRENT_DATE() and properties.user_id = "' . $uID . '"');
            $stmt->execute();
            $result = $stmt->fetchAll();
        } else {
            $stmt = $conn->prepare('SELECT properties.id,properties.name,properties.address,properties.zip,properties.about_properties,properties.additional_details,properties.amenities,properties.of_baths,properties.of_beds,properties.square_footage,cities.name as city_name, cities.country, cities.latitude, cities.longitude,property_repost_requests.note,property_repost_requests.status as repost_request_status FROM properties inner join cities on cities.id = properties.city_id inner join property_repost_requests on property_repost_requests.property_id = properties.id where property_repost_requests.status = "new" and DATE(properties.updated_date) = CURRENT_DATE() and properties.user_id = "' . $uID . '"');
            $stmt->execute();
            $result = $stmt->fetchAll();
        }

        /** Start XML file, echo parent node */
        $rootNode = new \SimpleXMLElement("<?xml version='1.0' encoding='UTF-8' standalone='yes'?><properties></properties>");

        foreach ($result as $row) {
            // ADD TO XML DOCUMENT NODE
            $itemNode = $rootNode->addChild('property');
            $itemNode->addChild('name', $row['name']);
            $itemNode->addChild('address', $row['address']);
            $itemNode->addChild('city_name', $row['city_name']);
            $itemNode->addChild('latitude', $row['latitude']);
            $itemNode->addChild('longitude', $row['longitude']);
            $itemNode->addChild('zip', $row['zip']);
            $itemNode->addChild('about_properties', $row['about_properties']);
            $itemNode->addChild('amenities', $row['amenities']);
            $itemNode->addChild('of_beds', $row['of_beds']);
            $itemNode->addChild('of_baths', $row['of_baths']);
            $itemNode->addChild('repost_request_status', $row['repost_request_status']);
            $itemNode->addChild('note', $row['note']);
        }

        /** check type status for delete and active post */
        if ($type != '') {
            $filename = "deleted-post-data.xml";
        } else {
            $filename = "active-post-data.xml";
        }

        return new Response($rootNode->asXML(), 200, array(
            'X-Sendfile' => $filename,
            'Content-type' => 'application/octet-stream',
            'Content-Disposition' => sprintf('attachment; filename="%s"', $filename)
        ));
    }

    /**
     * @Security("is_granted('ROLE_MANAGER') or is_granted('ROLE_LANDLORD')")
     * 
     * @param Request $request
     * @param int $propertyId
     * @return Response
     */
    public function transactionsAction(Request $request, $propertyId) {
        try {
            /** @var TokenStorage $tokenStorage */
            $tokenStorage = $this->get('security.token_storage');

            $requestStack = $this->get('request_stack');
            $masterRequest = $requestStack->getMasterRequest();

            $form = $this->createForm(new TransactionFilterType($tokenStorage));
            $form->handleRequest($masterRequest);

            $data = $form->getData();

            $dateFrom = $data['dateFrom'];
            $dateTo = $data['dateTo'];
            $keyword = $data['keyword'];

            $property = $this->em->getRepository(Property::REPOSITORY)->find($propertyId);
            $transactions = $this->em->getRepository(\Erp\StripeBundle\Entity\Transaction::REPOSITORY)
                    ->findTransactionsByProperty($property, $dateFrom, $dateTo, $keyword);
            $deposit = $property->getSecurityDeposit();

            $arrayDeposit = array();
            if ($deposit) {
                $status = $deposit->getStatus();

                $arrayDeposit = array(
                    'date' => ($status == PropertySecurityDeposit::STATUS_DEPOSIT_PAID) ? $deposit->getPaidAt() : $deposit->getRefundedAt(),
                    'paymentMethodDescription' => 'Deposit',
                    'internalType' => $deposit->getStatus(),
                    'metadata' => 'N/A',
                    'status' => $deposit->getStatus(),
                    'amount' => ($status == PropertySecurityDeposit::STATUS_DEPOSIT_PAID) ? $deposit->getAmount() : $deposit->getRefundedAmount(),
                    'balance' => 'N/A',
                    'fullName' => $property->getTenantUser()->getFullName()
                );
            }

            array_push($transactions, $arrayDeposit);
            usort($transactions, array($this, 'sortTransactionsByTenant'));

            $currentPage = $request->query->getInt('page', 1);
            $paginator = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                    $transactions, $currentPage, Property::LIMIT_USER_LISTINGS
            );

            $prevPage = ceil($pagination->getTotalItemCount() / Property::LIMIT_USER_LISTINGS);
            if ($prevPage && ($currentPage > $prevPage)) {
                return $this->redirectToRoute('erp_property_listings_transactions', ['propertyId' => $propertyId, 'page' => $prevPage]);
            }

            return $this->render('ErpPropertyBundle:Listings:property-transactions.html.twig', [
                        'pagination' => $pagination,
                        'property' => $property,
                        'form' => $form->createView(),
            ]);
        } catch (Exception $ex) {
            return $this->createNotFoundException();
        }
    }

    /**
     * Pre validate files
     *
     * @param Request  $request
     * @param Property $property
     * @param string   $formName
     * @param array    $fields
     *
     * @return array
     */
    private function preValidateFiles(Request $request, Property $property, $formName, $fields) {
        $files = $request->files->get($formName);
        $data = $request->request->get($formName);

        $errors = [];

        if (isset($files[$fields[0]]) && isset($data[$fields[0]])) {
            $files = $files[$fields[0]];

            $data = $data[$fields[0]];

            foreach ($data as $key => $item) {
                switch ($fields[0]) {
                    /* Documents */
                    case 'documents':
                        $file = new Document();
                        $file->setFile($files[$key][$fields[1]]);
                        $file->setOriginalName($item['originalName']);
                        $property->addDocument($file);
                        break;
                    /* Images */
                    case 'images':
                        $file = new Image();
                        $file->setImage($files[$key][$fields[1]]);
                        $property->addImage($file);
                        break;
                    /* Default */
                    default:
                        throw $this->createNotFoundException();
                }

                /** @var $errors \Symfony\Component\Validator\ConstraintViolationListInterface */
                $errorsValidate = $this->get('validator')->validate($file, null, ['EditProperty']);
                if ($errorsValidate->count()) {
                    unset($data[$key]);
                    unset($files[$key]);

                    $errors[] = $errorsValidate->get(0)->getMessage();
                }
            }

            $request->files->set($formName, [$fields[0] => $files]);
            $request->request->set($formName, [$fields[0] => $data]);
        }

        return ['request' => $request, 'errors' => $errors];
    }

    /**
     * Create form for property documents
     *
     * @param Property $property
     * @param int      $pageNumber
     *
     * @return \Symfony\Component\Form\Form
     */
    private function createEditDocumentPropertyForm(Property $property, $pageNumber = 1) {
        $action = $this->generateUrl(
                'erp_property_listings_edit_documents', ['propertyId' => $property->getId(), 'page' => $pageNumber]
        );

        $formOptions = ['action' => $action, 'method' => 'POST'];
        $form = $this->createForm(new EditDocumentPropertyFormType(), $property, $formOptions);

        return $form;
    }

    /**
     * Create property import form
     *
     * @return \Symfony\Component\Form\Form
     */
    private function createPropertyImportFormType() {
        $action = $this->generateUrl('erp_property_listings_all');
        $formOptions = ['action' => $action, 'method' => 'POST'];
        $form = $this->createForm(new PropertyImportFormType(), null, $formOptions);
        return $form;
    }

    /**
     * Create search bar property form
     *
     * @return \Symfony\Component\Form\Form
     */
    private function createSearchBarPropertyFormType() {
        $action = $this->generateUrl('erp_property_listings_all');
        $formOptions = ['action' => $action, 'method' => 'GET'];
        $form = $this->createForm(new PropertySearchBarFormType(), null, $formOptions);
        return $form;
    }

}
