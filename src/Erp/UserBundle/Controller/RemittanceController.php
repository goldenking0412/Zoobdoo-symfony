<?php

namespace Erp\UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Erp\CoreBundle\Controller\BaseController;
use Erp\UserBundle\Form\Type\RemittanceFilterType;
use Erp\UserBundle\Entity\Remittance;
use Erp\StripeBundle\Form\Type\AbstractFilterType;
use Erp\UserBundle\Form\Type\RemittanceType;

class RemittanceController extends BaseController {

    /**
     * @Security("is_granted('ROLE_MANAGER')")
     */
    public function listAction(Request $request, $_format = 'html') {
        /** @var User $user */
        $user = $this->getUser();

        $masterRequest = $this->get('request_stack')->getMasterRequest();

        $form = $this->createForm(new RemittanceFilterType($this->get('security.token_storage')));
        $form->handleRequest($masterRequest);

        $data = $form->getData();
        
        $dateFrom = $dateTo = $keyword = $property = $type = null;
        if ($masterRequest->query->get('f') == 'remittances') {
            $dateFrom = $data['dateFrom'];
            $dateTo = $data['dateTo'];
            $keyword = $data['keyword'];
            $property = $data['property'];
            $type = $data['type'];
        }

        $pagination = array();
        $repository = $this->em->getRepository(Remittance::REPOSITORY);
        $query = $repository->getRemittancesSearchQuery($dateFrom, $dateTo, $property, $type, $keyword);

        if ($_format == 'html') {
            $paginator = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                    $query, $request->query->getInt('page', 1)
            );
        } elseif ($_format == 'pdf') {
            $pagination = $query->getResult();
        }

        $template = sprintf('ErpUserBundle:Remittance:list.%s.twig', $_format);
        $parameters = [
            'user' => $user,
            'form' => $form->createView(),
            'pagination' => $pagination,
        ];

        if ($_format == 'html') {
            $urlParameters = array_merge(
                    array('_format' => 'pdf'), array('filter' => $this->getFilterParameters($masterRequest))
            );
            $parameters['pdf_link'] = $this->generateUrl('erp_user_remittances_list', $urlParameters);

            return $this->render($template, $parameters);
        } elseif ($_format == 'pdf') {
            $fileName = sprintf('zoobdoo_remittances_%s.pdf', (new \DateTime())->format('d_m_Y'));
            $html = $this->renderView($template, $parameters);
            $pdf = $this->get('knp_snappy.pdf')->getOutputFromHtml($html);
            
            return $this->pdfResponse($pdf, $fileName);
        }
    }

    /**
     * @Security("is_granted('ROLE_MANAGER')")
     */
    public function createAction(Request $request) {
        $remittance = new Remittance();
        $remittance->setManager($this->getUser());

        return $this->manageFormRequest($request, $remittance);
    }

    /**
     * @Security("is_granted('ROLE_MANAGER')")
     */
    public function editAction(Request $request, $remittanceId) {
        $remittance = $this->em->getRepository(Remittance::REPOSITORY)->find($remittanceId);

        return $this->manageFormRequest($request, $remittance);
    }

    /**
     * @Security("is_granted('ROLE_MANAGER')")
     */
    public function deleteAction(Request $request, $remittanceId) {
        $remittance = $this->em->getRepository(Remittance::REPOSITORY)->find($remittanceId);
        
        if ($request->getMethod() === 'DELETE') {
            $this->em->remove($remittance);
            $this->em->flush();

            return $this->redirect($request->headers->get('referer'));
        } else {
             return $this->render('ErpCoreBundle:crossBlocks:delete-confirmation-popup.html.twig', array(
                'askMsg' => 'Are you sure you want to delete this remittance?',
                'actionUrl' => $this->generateUrl(
                        'erp_user_remittances_delete', array('remittanceId' => $remittance->getId())
                ),
                'actionMethod' => 'DELETE'
            ));
        }
    }

    /**
     * 
     * @param Request $request
     * @param Remittance $remittance
     * @return Form
     */
    private function manageFormRequest(Request $request, Remittance $remittance) {
        $form = $this->createForm(RemittanceType::class, $remittance);
        $form->handleRequest($request);

        $template = 'ErpUserBundle:Remittance:form.html.twig';
        if ($form->isSubmitted() && $form->isValid()) {
            $type = $form->get('type')->getData();
            $types = Remittance::getTypeOptions();

            $remittance->setCurrency('usd');
            $remittance->setType($types[$type]);

            $this->em->persist($remittance);
            $this->em->flush();

            return $this->render($template, array(
                        'form' => $form->createView(),
                        'remittance' => null,
                        'submitted' => true,
            ));
        }

        return $this->render($template, array(
                    'form' => $form->createView(),
                    'remittance' => $remittance,
                    'submitted' => false,
        ));
    }

    /**
     * 
     * @param Request $request
     * @return type
     */
    private function getFilterParameters(Request $request) {
        return $request->query->get(AbstractFilterType::NAME, []);
    }

}
