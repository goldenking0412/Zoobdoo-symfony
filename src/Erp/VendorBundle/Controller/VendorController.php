<?php

namespace Erp\VendorBundle\Controller;

use Erp\CoreBundle\Controller\BaseController;
use Erp\VendorBundle\Entity\VendorCreate;
use Erp\VendorBundle\Form\VendorCreateType;
use Erp\VendorBundle\Form\VendorEditType;
use Erp\VendorBundle\Form\VendorType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

class VendorController extends BaseController {

    /**
     * @Security("is_granted('ROLE_MANAGER')")
     */
    public function indexAction(Request $request) {
        $repository = $this->getDoctrine()->getRepository(VendorCreate::class);

        $query = $repository->getVendorQuery();

        $currentPage = 1;
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query, $currentPage, $request->query->getInt('page', 3)
        );

        $parameters = [
            'pagination' => $pagination
        ];

        return $this->render('ErpVendorBundle::index.html.twig', $parameters);
    }

    /**
     * @Security("is_granted('ROLE_MANAGER')")
     */
    public function createAction(Request $request) {
        $user = $this->getUser();

        $vendor = new VendorCreate();
        $form = $this->createForm(new VendorCreateType(), $vendor);

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->em->persist($vendor);
                $this->em->flush();

                $this->addFlash('alert_ok', 'Vendor has been added successfully!');

                /** @var Erp\UserBundle\Mailer\Processor $mailerService */
                $mailerService = $this->get('erp_user.mailer.processor');
                $mailerService->sendVendorCreatedEmail($vendor, $user);

                return $this->redirect($this->generateUrl('erp_vendor_index'));
            }
        }

        return $this->render('ErpVendorBundle:Form:create.html.twig', [
                    'user' => $user,
                    'form' => $form->createView()
        ]);
    }

    /**
     * @Security("is_granted('ROLE_MANAGER')")
     */
    public function editAction(Request $request, $vendorId) {
        $vendor = $this->em->getRepository(VendorCreate::REPOSITORY)->find($vendorId);

        $vendorEdit = ($vendor->hasVendorEdit())
                ? $vendor->getLastVendorEdit()
                : new \Erp\VendorBundle\Entity\VendorEdit()
        ;
        $form = $this->createForm(new VendorEditType(), $vendorEdit);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newVendorEdit = clone $form->getData();
            
            $vendor->addVendorEdit($newVendorEdit);
            
            $this->em->persist($vendor);
            $this->em->flush();
            
            $this->addFlash('alert_ok', 'Vendor has been updated successfully!');
            return $this->redirect($this->generateUrl('erp_vendor_index'));
        }

        return $this->render('ErpVendorBundle:Form:edit.html.twig', [
                    'vendor' => $vendor,
                    'form' => $form->createView()
        ]);
    }

    /**
     * @Security("is_granted('ROLE_MANAGER')")
     */
    public function deleteAction(Request $request, $vendorId) {
        $vendor = $this->em->getRepository(VendorCreate::REPOSITORY)->find($vendorId);

        if ($request->getMethod() === 'DELETE') {
            $this->em->remove($vendor);
            $this->em->flush();

            return $this->redirect($this->generateUrl('erp_vendor_index'));
        }

        return $this->render('ErpCoreBundle:crossBlocks:delete-confirmation-popup.html.twig', [
                    'askMsg' => 'Are you sure you want to delete this contractor?',
                    'actionUrl' => $this->generateUrl('erp_vendor_delete', ['vendorId' => $vendorId]),
                    'actionMethod' => 'DELETE'
        ]);
    }

}
