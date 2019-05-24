<?php

namespace Erp\NotificationBundle\Controller;

use Erp\CoreBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Erp\NotificationBundle\Entity\Template;
use Erp\NotificationBundle\Form\Type\TemplateType;
use Erp\UserBundle\Entity\User;

class TemplateController extends BaseController {

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction() {
        /** @var User $user */
        $user = $this->getUser();
        $repository = $this->em->getRepository(Template::class);
        $templates = $repository->getTemplatesByUser($user);

        return $this->render('ErpNotificationBundle:Template:list.html.twig', [
                    'templates' => $templates,
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request) {
        /** @var User $user */
        $user = $this->getUser();
        
        $entity = new Template();
        $entity->setUser($user);

        return $this->update($entity, $request);
    }

    /**
     * @param Template $entity
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Template $entity, Request $request) {
        $this->checkAccess($entity);
        return $this->update($entity, $request);
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeAction($id) {
        $repository = $this->em->getRepository(Template::class);

        /** @var Template $entity */
        $entity = $repository->find($id);
        $this->checkAccess($entity);

        $this->em->remove($entity);
        $this->em->flush();

        return $this->redirectToRoute('erp_notification_template_list');
    }

    /**
     * @param Template $entity
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function update(Template $entity, Request $request) {
        $isSms = ((int) $request->get('is_sms', 0));

        $form = $this->createForm(new TemplateType(), $entity, array('is_sms' => $isSms));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $isSms = $request->get('is_sms');
            $entity->setIsSms(((bool) $isSms));
            
            $this->em->persist($entity);
            $this->em->flush();

            $this->addFlash('alert_ok', 'Success');

            return $this->redirectToRoute('erp_notification_template_list');
        }

        return $this->render('ErpNotificationBundle:Template:create.html.twig', array(
                    'form' => $form->createView(),
                    'entity' => $entity,
                    'isSms' => $isSms
        ));
    }

    /**
     * @param Template $entity
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function printPdfAction(Template $entity, Request $request) {
        $this->checkAccess($entity);

        $html = $this->getTemplateManager()->renderTemplate($entity);
        $fileName = 'notification_template (' . $entity->getTitle() . ').pdf';
        $pdf = $this->get('knp_snappy.pdf')->getOutputFromHtml($html);
        return $this->pdfResponse($pdf, $fileName);
    }

    /**
     * 
     * @return type
     */
    private function getTemplateManager() {
        return $this->get('erp_notification.template_manager');
    }

    /**
     * 
     * @param Template $template
     * @throws type
     */
    private function checkAccess(Template $template) {
        $user = $this->getUser();
        if ($user !== $template->getUser()) {
            throw $this->createAccessDeniedException('You don\'t have access to this Template');
        }
    }

}
