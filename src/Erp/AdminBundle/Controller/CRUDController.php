<?php

namespace Erp\AdminBundle\Controller;

use Erp\CoreBundle\EmailNotification\EmailNotificationFactory;
use Erp\PropertyBundle\Entity\Property;
use Erp\UserBundle\Entity\ProReport;
use Erp\UserBundle\Entity\ProRequest;
use Erp\UserBundle\Entity\User;
use Sonata\AdminBundle\Controller\CRUDController as BaseController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Goodby\CSV\Export\Standard\ExporterConfig;
use Goodby\CSV\Export\Standard\Exporter;
use Goodby\CSV\Export\Standard\CsvFileObject;
use Goodby\CSV\Export\Standard\Collection\PdoCollection;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;

class CRUDController extends BaseController {
    /**
     * Send email to manager with invitation to complete profile
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Erp\CoreBundle\Exception\UserNotFoundExceptionct
     */

    /** send eviction to tenant and template download in admin side */
    public function listpdfAction(Request $request, $id = null) {
        $eid = $request->get('id');
        $conn = $this->get('database_connection');
        $stmt = $conn->prepare("SELECT * FROM erp_notification_template where erp_notification_template.id = '$eid'");
        $stmt->execute();
        $result = $stmt->fetch();

        $template = 'ErpNotificationBundle:Template:mail.html.twig';
        $fileName = 'notification_template (' . $result['title'] . ').pdf';
        $parameters = [
            'content' => $result['description'],
        ];
        $html = $this->renderView($template, $parameters);
        $pdf = $this->get('knp_snappy.pdf')->getOutputFromHtml($html);

        return new PdfResponse($pdf, $fileName);
    }

    /** get xml feed data in admin side */
    public function xmlAction(Request $request, $type = null) {

        $type = $request->get('type');
        $conn = $this->get('database_connection');

        /** check type status for delete and active post */
        $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/';

        if ($type == '1') {
            $stmt = $conn->prepare("SELECT 
                                    properties.id,
                                    properties.created_date,
                                    properties.updated_date,
                                    properties.name,
                                    properties_settings.payment_amount as properties_price,
                                    properties.address,
                                    properties.zip,
                                    properties.state_code,
                                    properties.about_properties,
                                    properties.additional_details,
                                    properties.amenities,
                                    properties.of_baths,
                                    properties.of_beds,
                                    properties.square_footage,
                                    cities.name as city_name,
                                    cities.country, 
                                    cities.latitude,
                                    cities.longitude,
                                    property_repost_requests.note,
                                    property_repost_requests.status as repost_request_status
                                    FROM properties 
                                    inner join cities on cities.id = properties.city_id 
                                    inner join property_repost_requests on property_repost_requests.property_id = properties.id 
                                    left join properties_settings on properties.settings_id = properties_settings.id
                                    where property_repost_requests.status = 'rejected' and DATE(property_repost_requests.updated_date) = CURRENT_DATE() group by properties.id");

            $stmt->execute();
            $result = $stmt->fetchAll();
        } else {
            $stmt = $conn->prepare("SELECT 
                                    properties.id,
                                    properties.created_date,
                                    properties.updated_date,
                                    properties.name,
                                    properties_settings.payment_amount as properties_price,
                                    properties.address,
                                    properties.zip,
                                    properties.state_code,
                                    properties.about_properties,
                                    properties.additional_details,
                                    properties.amenities,
                                    properties.of_baths,
                                    properties.of_beds,
                                    properties.square_footage,
                                    cities.name as city_name,
                                    cities.country, 
                                    cities.latitude,
                                    cities.longitude,
                                    property_repost_requests.note,
                                    property_repost_requests.status as repost_request_status
                                    FROM properties
                                    inner join cities on cities.id = properties.city_id 
                                    inner join property_repost_requests on property_repost_requests.property_id = properties.id
                                    left join properties_settings on properties.settings_id = properties_settings.id 
                                    where property_repost_requests.status != 'rejected' and DATE(properties.updated_date) = CURRENT_DATE() group by properties.id");

            $stmt->execute();
            $result = $stmt->fetchAll();
        }

        /** check type status for delete and active post for set @filename for sync or export data in current files that's in assets/web/xml => filename */
        if ($type != '') {
            $todaysFile = $_SERVER['DOCUMENT_ROOT'] . '/assets/xml/cancelled-post-data.xml';
        } else {
            $todaysFile = $_SERVER['DOCUMENT_ROOT'] . '/assets/xml/active-post-data.xml';
        }

        /** to check file exist or not */
        if (file_exists($todaysFile)) {

            /** to check file exist and have writable permission */
            if (is_writable($todaysFile)) {

                $xmlFileContents = file_get_contents($todaysFile);
                $rootNode = new \SimpleXMLElement("<?xml version='1.0' encoding='UTF-8' standalone='yes'?><Properties></Properties>");

                /** Start XML file, echo parent node */
                if ($result) {
                    /** fetch data in foreach loop to add in xml document */
                    foreach ($result as $row) {
                        $stmt_img = $result_img = '';
                        $stmt_img = $conn->prepare("SELECT 
                                    property_images.property_id,
                                    CONCAT('$baseurl',images.path,'/', images.name) AS image_path
                                    FROM property_images 
                                    left join  images on property_images.image_id = images.id 
                                    where property_images.property_id = '" . $row['id'] . "'");

                        $stmt_img->execute();
                        $result_img = $stmt_img->fetchAll();

                        /** set post property created and updated date */
                        $created_date = date('Y-m-d H:i:s', strtotime($row['created_date']));
                        $updated_date = date('Y-m-d H:i:s', strtotime($row['updated_date']));

                        /** Add data in document xml node */
                        $itemNode = $rootNode->addChild('Property');
                        $itemNode->addAttribute('LocalPropertyID', $row['id']);
                        $itemNode->addChild('LegalName', $row['name']);
                        $itemNode->addChild('Description', $row['about_properties']);
                        $itemNode->addChild('Price', $row['properties_price']);
                        $itemNode->addChild('Address', $row['address']);
                        $itemNode->addChild('City', $row['city_name']);
                        $itemNode->addChild('State', $row['state_code']);
                        $itemNode->addChild('Zip', $row['zip']);
                        $itemNode->addChild('Latitude', $row['latitude']);
                        $itemNode->addChild('Longitude', $row['longitude']);
                        $itemNode->addChild('Amenities', $row['amenities']);
                        $itemNode->addChild('Bedrooms', $row['of_beds']);
                        $itemNode->addChild('FullBaths', $row['of_baths']);
                        $itemNode->addChild('SquareFeet', $row['square_footage']);
                        foreach ($result_img as $row_img) {
                            $data = $itemNode->addChild('PropertyPhoto', $row_img['image_path']);
                            $data->addAttribute('ImageUrl', $row_img['image_path']);
                        }
                        $itemNode->addChild('Modified_date', $updated_date);
                    }
                } else {
                    /** Set Blank data if didn't have any posts for both case cancelled and modified post */
                    $itemNode = $rootNode->addChild('Property');
                    if ($type == '1') {
                        $itemNode->addChild('empty', 'today cancelled post record not found');
                    } else {
                        $itemNode->addChild('empty', 'today modified post record not found');
                    }
                }

                /** Save out the xml file update set xml node and data */
                $rootNode->asXML($todaysFile);

                /** return redirect for export data as xml  */
                $this->addFlash('sonata_flash_success', 'Sync Xml file update successfully');
                $referer = $this->getRequest()->headers->get('referer');
                return $this->redirect($referer);
            } else {
                /** return redirect for export data as xml if file didn't have writable permission */
                $this->addFlash('sonata_flash_error', 'xml file does not exist or is not writable on this path ' . $todaysFile . '. Please, try again later');
                $referer = $this->getRequest()->headers->get('referer');
                return $this->redirect($referer);
            }
        } else {
            /** return redirect for export data as xml if file didn't exist */
            $this->addFlash('sonata_flash_error', 'xml file does not exist on this path ' . $todaysFile . '. Please, try again later');
            $referer = $this->getRequest()->headers->get('referer');
            return $this->redirect($referer);
        }

        /* return new Response($rootNode->asXML(), 200,array(
          'X-Sendfile'          => $filename,
          'Content-type'        => 'application/octet-stream',
          'Content-Disposition' => sprintf('attachment; filename="%s"', $filename)
          )); */
    }

    /** In admin side export data function for CSV file */
    public function csvAction(Request $request) {

        $type = $request->get('type');
        $conn = $this->get('database_connection');

        /** check type status for delete and active post */
        $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/';

        if ($type == '1') {
            $stmt = $conn->prepare("SELECT 
                                    properties.id,
                                    properties.created_date,
                                    properties.updated_date,
                                    properties.name,
                                    properties.address,
                                    properties.zip,
                                    properties.state_code,
                                    properties.about_properties,
                                    properties.additional_details,
                                    properties.amenities,
                                    properties.of_baths,
                                    properties.of_beds,
                                    properties.square_footage,
                                    cities.name as city_name,
                                    cities.country,
                                    cities.latitude,
                                    cities.longitude,
                                    CONCAT('$baseurl',images.path,'/', images.name) AS image_path,
                                    property_repost_requests.note,
                                    property_repost_requests.status as repost_request_status
                                    FROM properties 
                                    inner join cities on cities.id = properties.city_id 
                                    inner join property_repost_requests on property_repost_requests.property_id = properties.id
                                    left join property_images on property_images.property_id = properties.id
                                    left join  images on property_images.image_id = images.id 
                                    where property_repost_requests.status = 'rejected' and DATE(property_repost_requests.updated_date) = CURRENT_DATE() group by properties.id");
            $stmt->execute();
        } else {
            $stmt = $conn->prepare("SELECT 
                                    properties.id,
                                    properties.created_date,
                                    properties.updated_date,
                                    properties.name,
                                    properties.address,
                                    properties.zip,
                                    properties.state_code,
                                    properties.about_properties,
                                    properties.additional_details,
                                    properties.amenities,
                                    properties.of_baths,
                                    properties.of_beds,
                                    properties.square_footage,
                                    cities.name as city_name, 
                                    cities.country, cities.latitude, 
                                    cities.longitude,
                                    CONCAT('$baseurl',images.path,'/', images.name) AS image_path,
                                    property_repost_requests.note,
                                    property_repost_requests.status as repost_status
                                    FROM properties 
                                    inner join cities on cities.id = properties.city_id 
                                    inner join property_repost_requests on property_repost_requests.property_id = properties.id
                                    left join property_images on property_images.property_id = properties.id
                                    left join  images on property_images.image_id = images.id 
                                    where property_repost_requests.status != 'rejected' and DATE(properties.updated_date) = CURRENT_DATE() group by properties.id");
            $stmt->execute();
        }

        /** create new stream response object */
        $response = new StreamedResponse();
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment');

        /** check type status for delete and active post to set @filename for export data */
        if ($type != '') {
            $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', 'cancelled-post-data.csv'));
        } else {
            $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', 'active-post-data.csv'));
        }

        /** response callback function for set data and header */
        $response->setCallback(function() use($stmt) {

            $results = $stmt->fetch();
            if ($results) {
                /** Add keys in ne @keyArray for set in config column headers */
                $keyArray = array();
                foreach ($results as $key => $val) {
                    $keyArray[] = $key;
                }

                /** Create ExporterConfig object and setting config for csv file */
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
                /** Set Blank data if didn't have any modified and cancelled posts */
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
     * Send email to manager with invitation to complete profile
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Erp\CoreBundle\Exception\UserNotFoundExceptionct
     */
    public function sentInviteAction() {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->admin->getSubject();
        $isConfirmed = false;
        $isPending = false;

        if ($user) {
            $isConfirmed = $user->getStatus() == User::STATUS_NOT_CONFIRMED;
            $isPending = $user->getStatus() == User::STATUS_PENDING;
        }

        if (!$user || (!$isConfirmed && !$isPending)) {
            throw $this->createNotFoundException();
        }

        $emailParams = [
            'sendTo' => $user->getEmail(),
            'url' => $this->generateUrl('fos_user_security_login', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ];
        $emailType = EmailNotificationFactory::TYPE_MANAGER_COMPLETE_PROFILE;
        $isSent = $this->get('erp.core.email_notification.service')->sendEmail($emailType, $emailParams);

        if ($isSent) {
            $this->addFlash('sonata_flash_success', 'Email successfully send');
        } else {
            $this->addFlash('sonata_flash_error', 'An error occurred while sending a message. Please, try again later');
        }

        return $this->redirect($this->generateUrl('admin_erpuserbundle_managers_list'));
    }

    /**
     * Delete landlord
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteLandlordAction() {
        $this->deleteUser();

        return $this->redirect($this->generateUrl('admin_erpuserbundle_landlords_list'));
    }

    /**
     * Delete tenant
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteTenantAction() {
        $this->deleteUser();

        return $this->redirect($this->generateUrl('admin_erpuserbundle_tenants_list'));
    }

    /**
     * Delete manager
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteManagerAction() {
        $this
                ->getDoctrine()
                ->getManager()
                ->getRepository(\Erp\PropertyBundle\Entity\Property::REPOSITORY)
                ->nullifyTenantUserOfPropertyByUser($this->admin->getSubject())
        ;
        
        $this->deleteUser();

        return $this->redirect($this->generateUrl('admin_erpuserbundle_managers_list'));
    }

    /**
     * Disable Manager
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function disableManagerAction() {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->admin->getSubject();
        if (!$user || !$user->isActive()) {
            throw $this->createNotFoundException();
        }

        $em = $this->getDoctrine()->getManager();
        
        /** @var Property $property */
        foreach ($user->getProperties() as $property) {
            $invitedUsers = $property->getInvitedUsers();
            foreach ($invitedUsers as $invitedUser) {
                $em->remove($invitedUser);
                $em->flush();
            }
            $em->persist($property);
        }
        
        // remove notification histories of properties
        $notificationHistories = $em->getRepository(\Erp\NotificationBundle\Entity\History::REPOSITORY)->getHistoryByUser($user);
        foreach ($notificationHistories as $item) {
            $em->remove($item);
        }

        // clear properties
        $user->clearProperties();
        // deactivate user
        $this->get('erp.users.user.service')->deactivateUser($user);

        foreach ($user->getStripeCustomers() as $stripeCustomer) {
            $apiManager = $this->get('erp_stripe.entity.api_manager');
            
            $stripeSubscription = $stripeCustomer->getStripeSubscription();

            if (!$stripeSubscription) {
                $this->addFlash(
                        'sonata_flash_error', 'The user does not have subscription'
                );
                return $this->redirect($this->get('request')->headers->get('referer'));
            }

            $arguments = [
                'id' => $stripeSubscription->getSubscriptionId(),
                'options' => null,
            ];

            $response = $apiManager->callStripeApi('\Stripe\Subscription', 'retrieve', $arguments);

            if (!$response->isSuccess()) {
                $this->addFlash(
                        'sonata_flash_error', 'An occurred error while retrieving subscription'
                );
                return $this->redirect($this->get('request')->headers->get('referer'));
            }

            /** @var \Stripe\Subscription $subscription */
            $subscription = $response->getContent();
            $response = $apiManager->callStripeObject($subscription, 'cancel');

            if (!$response->isSuccess()) {
                $this->addFlash(
                        'sonata_flash_error', 'An occurred error while cancel subscription'
                );
                return $this->redirect($this->get('request')->headers->get('referer'));
            }

            $em->remove($stripeCustomer);
        }
        
        $em->flush();

        $this->addFlash(
                'sonata_flash_success', 'Success'
        );

        return $this->redirect($this->get('request')->headers->get('referer'));
    }

    /**
     * Disable Admin
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function disableAdminAction() {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->admin->getSubject();
        if (!$user || $user->getStatus() !== User::STATUS_ACTIVE) {
            throw $this->createNotFoundException();
        }

        $this->get('erp.users.user.service')->deactivateUser($user);

        $user->setStatus(User::STATUS_DISABLED)
                ->setEnabled(false);
        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($user);

        $em->flush();

        return $this->redirect($this->generateUrl('admin_erpuserbundle_administrators_list'));
    }

    /**
     * Reject Manager
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function rejectManagerAction() {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->admin->getSubject();
        if (!$user || $user->getStatus() == User::STATUS_ACTIVE) {
            throw $this->createNotFoundException();
        }

        $this->get('erp.users.user.service')->deactivateUser($user);

        $user->setStatus(User::STATUS_REJECTED)
                ->setEnabled(false);
        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($user);
        $em->flush();

        return $this->redirect($this->get('request')->headers->get('referer'));
    }

    /**
     * Removing tenant from property
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeTenantAction() {
        /** @var $property \Erp\PropertyBundle\Entity\Property */
        $property = $this->admin->getSubject();
        $tenant = $property->getTenantUser();
        if (!$tenant or $tenant->getStatus() == User::STATUS_DISABLED) {
            throw $this->createNotFoundException();
        }

        $userService = $this->get('erp.users.user.service');
        $userService->deactivateUser($tenant);
        $userService->setStatusUnreadMessages($tenant);

        $property->setStatus(Property::STATUS_DRAFT)->setTenantUser(null);
        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($property);
        $em->flush();

        $this->addFlash(
                'sonata_flash_success', 'Tenant was removed successfully and all future tenant payments were cancelled'
        );

        return $this->redirect($this->generateUrl('admin_erpuserbundle_properties_list'));
    }

    /**
     * Add manager to report
     *
     * @param ProRequest $proRequest
     * @param \DateTime  $date
     *
     * @return $this
     */
    protected function addToReport(ProRequest $proRequest, $date) {
        $month = $date->format('F');
        $em = $this->getDoctrine()->getEntityManager();
        $report = $em->getRepository('ErpUserBundle:ProReport')->getByConsultantAndMonth(
                $proRequest->getProConsultant(), $month
        );

        if ($report) {
            $report->setCountUsers($report->getCountUsers() + 1);
        } else {
            $report = new ProReport();
            $report->setProConsultant($proRequest->getProConsultant())
                    ->setApprovedDate($date)
                    ->setCountUsers(1);
        }

        $em->persist($report);
        $em->flush();

        return $this;
    }

    /**
     * Delete User
     *
     * @return $this
     */
    protected function deleteUser() {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->admin->getSubject();

        if (!$user || !in_array($user->getStatus(), [User::STATUS_REJECTED, User::STATUS_DISABLED])) {
            throw $this->createNotFoundException();
        }

        $em = $this->getDoctrine()->getEntityManager();
        $em->remove($user);
        $em->flush();

        $this->addFlash('sonata_flash_ps_success', 'User was successfully deleted');

        return $this;
    }

}
