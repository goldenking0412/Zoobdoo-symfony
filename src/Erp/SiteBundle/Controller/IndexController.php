<?php

namespace Erp\SiteBundle\Controller;

use Erp\CoreBundle\Controller\BaseController;
use Erp\SiteBundle\Entity\ContactPageRequest;
use Erp\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Erp\SiteBundle\Form\Type\ManagerInviteFormType;
use Erp\CoreBundle\EmailNotification\EmailNotificationFactory;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Erp\SiteBundle\Form\Type\ContactPageRequestFormType;
use Erp\PropertyBundle\Entity\Property;

class IndexController extends BaseController {

    /**
     * Homepage
     *
     * @return Response
     */
    public function indexAction() {
        return $this->render('ErpSiteBundle:Home:index.html.twig');
    }

    /**
     * 
     * @return type
     */
    public function listingPropertiesAction() {
        $properties = $this->em->getRepository(Property::REPOSITORY)->findAvailable($this->getUser());

        return $this->render('ErpSiteBundle:Home:properties.html.twig', array(
                    'properties' => $properties,
        ));
    }

    /**
     * Return static page
     *
     * @param string $slug
     *
     * @return Response
     */
    public function staticPageAction($slug) {
        return $this->render('ErpSiteBundle:StaticPage:' . $slug . '.html.twig');
    }

    /**
     * Contact form
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function contactPageAction(Request $request) {
        $contactPageRequest = new ContactPageRequest();
        
        $formOptions = array('action' => $this->generateUrl('erp_site_contact_page'), 'method' => 'POST');
        $form = $this->createForm(new ContactPageRequestFormType(), $contactPageRequest, $formOptions);

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                // send email
                $formData = $request->request->get($form->getName());

                $fees = $this->get('erp.core.fee.service')->getFees();
                $defaultEmail = $fees ? $fees->getDefaultEmail() : '';

                $emailParams = array(
                    'sendTo' => $defaultEmail,
                    'url' => $this->generateUrl(
                            'admin_erpsitebundle_contactpagerequests_list', [], UrlGeneratorInterface::ABSOLUTE_URL
                    ),
                    'formData' => $formData
                );

                $emailType = EmailNotificationFactory::TYPE_CONTACT_FORM_TO_ADMIN;
                $this->container->get('erp.core.email_notification.service')
                        ->sendEmail($emailType, $emailParams);

                $this->em->persist($contactPageRequest);
                $this->em->flush();
                $this->get('session')->getFlashBag()->add('alert_ok', 'Your message was sent successfully');

                return $this->redirectToRoute('erp_site_contact_page');
            }
        }

        return $this->render('ErpSiteBundle:StaticPage:contact.html.twig', array(
                    'form' => $form->createView(),
        ));
    }

    /**
     * Popup - send invite to manager
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function sendInviteToManagerAction(Request $request) {
        $action = $this->generateUrl('erp_site_send_invite_to_manager');

        $formOptions = ['action' => $action, 'method' => 'POST'];
        $form = $this->createForm(ManagerInviteFormType::class, null, $formOptions);

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $formData = $request->request->all()[$form->getName()];

                $emailParams = [
                    'sendTo' => $formData['managerEmail'],
                    'managerInvite' => $formData,
                ];

                /** @var User $toUser */
                $manager = $this->em->getRepository('ErpUserBundle:User')->findOneBy(array('email' => $formData['managerEmail']));

                if ($manager) {
                    if ($manager->getStatus() == User::STATUS_DISABLED || $manager->getStatus() == User::STATUS_REJECTED) {
                        // show error on contact page
                        $error = 'Invite to this Manager cannot be sent. Contact Administrator for details.';
                        $this->get('session')->getFlashBag()->add('alert_error', $error);
                        return $this->redirectToRoute('erp_site_contact_page');
                    } else {
                        $emailType = EmailNotificationFactory::TYPE_MANAGER_ACTIVE_INVITE;
                        $emailParams['url'] = $this->generateUrl('fos_user_security_login', [], UrlGeneratorInterface::ABSOLUTE_URL);
                    }
                } else {
                    $emailType = EmailNotificationFactory::TYPE_MANAGER_INVITE;
                    $emailParams['url'] = $this->generateUrl('fos_user_registration_register', [], UrlGeneratorInterface::ABSOLUTE_URL);
                }

                $this->container->get('erp.core.email_notification.service')->sendEmail($emailType, $emailParams);
                return $this->redirectToRoute('erp_site_homepage');
            }
        }

        return $this->render('ErpSiteBundle:Form:send-invite-to-manager-form.html.twig', array(
                    'form' => $form->createView()
        ));
    }

}
