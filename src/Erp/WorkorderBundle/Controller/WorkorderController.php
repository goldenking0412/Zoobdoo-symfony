<?php

namespace Erp\WorkorderBundle\Controller;

use Erp\VendorBundle\Entity\VendorCreate;
use Erp\WorkorderBundle\Entity\Workorder;
use Erp\WorkorderBundle\Entity\WorkorderService;
use Erp\WorkorderBundle\Form\WorkorderType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Erp\CoreBundle\Controller\BaseController;

class WorkorderController extends BaseController {

    /**
     * @Security("is_granted('ROLE_MANAGER')")
     */
    public function indexAction(Request $request) {
        return $this->render('ErpWorkorderBundle:Workorder:index.html.twig');
    }

    /**
     * @Security("is_granted('ROLE_MANAGER')")
     */
    public function showWorkorderAction(Request $request) {
        /** @var TokenStorage $tokenStorage */
        $tokenStorage = $this->get('security.token_storage');
        /** @var User $user */
        $user = $tokenStorage->getToken()->getUser();
        $account = $user->getStripeAccount();

        $pagination = [];
        if ($account) {
            $accountId = $account ? $account->getId() : null;

            $query = $this->em->getRepository(Workorder::class)->getWorkOrderQuery($accountId);

            $paginator = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                    $query, $request->query->getInt('page', 1)
            );
        }

        $vendor_list = $this->em->getRepository(VendorCreate::class)->getVendorList();

        $template = sprintf('ErpWorkorderBundle:Workorder:workorder_list.html.twig');
        $parameters = [
            'user' => $user,
            'contractor' => $vendor_list,
            'pagination' => $pagination,
        ];

        return $this->render($template, $parameters);
    }

    /**
     * @Security("is_granted('ROLE_MANAGER')")
     */
    public function createAction(Request $request) {
        $user = $this->getUser();
        $manager = $user->getStripeAccount();
        $managerId = ($manager) ? $manager->getId() : null;
        
        $action = $this->generateUrl('erp_workorder_create');
        $formOptions = ['action' => $action, 'method' => 'POST'];

        $vendor_list = $this->em->getRepository(VendorCreate::class)->getVendorList();

        $workorder = new Workorder();
        $form = $this->createForm(new WorkorderType($managerId, $vendor_list), $workorder, $formOptions);

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {

                var_dump($workorder->getStatus());
                /* $this->em->persist($workorder);
                $this->em->flush();*/

                $serviceData = $request->request->get('serviceData');
                if ($serviceData != '') {
                    $serviceData = json_decode($serviceData, true);

                    foreach ($serviceData as $item) {
                        $serviceEntity = new WorkorderService();

                        $serviceEntity->setTaskName($item['task_name']);
                        $serviceEntity->setHours($item['hours']);
                        $serviceEntity->setRate($item['rate']);
                        $serviceEntity->setTaxCode($item['tax_code']);
                        $serviceEntity->setWorkorder($workorder);
                        $serviceEntity->setActions(1);

                        /*$this->em->persist($serviceEntity);
                        $this->em->flush(); */
                    }
                }

                $this->addFlash('alert_ok', 'Work Order has been added successfully!');

                // return $this->redirect($this->generateUrl('erp_workorder_index'));
            }
        }

        return $this->render('ErpWorkorderBundle:Workorder:edit.html.twig', array(
                    'user' => $user,
                    'form' => $form->createView()
        ));
    }

    /**
     * @Security("is_granted('ROLE_MANAGER')")
     * 
     * @param Request $request
     * @param integer $workorderId
     * @return Response
     */
    public function updateAction(Request $request, $workorderId) {
        $user = $this->getUser();
        $manager = $user->getStripeAccount();

        $action = $this->generateUrl('erp_workorder_update');
        $formOptions = ['action' => $action, 'method' => 'POST'];

        $workorder = $this->em->getRepository(Workorder::REPOSITORY)->find($workorderId);
        $form = $this->createForm(new WorkorderType($manager->getId()), $workorder, $formOptions);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->em->persist($workorder);
            $this->em->flush();

            $this->addFlash('alert_ok', 'Work Order has been added successfully!');

            return $this->redirect($this->generateUrl('erp_workorder_index'));
        }

        return $this->render('ErpWorkorderBundle:Workorder:edit.html.twig', [
                    'user' => $user,
                    'form' => $form->createView()
        ]);
    }

}
