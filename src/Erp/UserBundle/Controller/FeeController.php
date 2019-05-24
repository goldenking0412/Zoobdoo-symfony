<?php

namespace Erp\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Erp\UserBundle\Entity\User;
use Erp\UserBundle\Form\Type\FeeType;
use Erp\UserBundle\Entity\Fee;
use Erp\CoreBundle\Controller\BaseController;

class FeeController extends BaseController
{
    /**
     * @param User $user
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction(User $user, Request $request)
    {
        if (!$user->hasRole(User::ROLE_TENANT)) {
            throw $this->createAccessDeniedException();
        }

        $entity = new Fee();
        $entity->setUser($user);

        return $this->update($entity, $request);
    }

    /**
     * @param Fee $fee
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function updateAction(Fee $fee, Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($user->hasTenant($fee->getUser())) {
            return $this->createNotFoundException();
        }

        return $this->update($fee, $request);
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function removeAction($id, Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();

        $repository = $this->em->getRepository(Fee::class);
        $fee = $repository->find($id);

        if ($user->hasTenant($fee->getUser())) {
            return $this->createNotFoundException();
        }

        if ($request->getMethod() === 'DELETE') {
            $this->em->remove($fee);
            $this->em->flush();

            $this->addFlash(
                'alert_ok',
                'Success'
            );

            return $this->redirectToRoute('erp_user_dashboard_dashboard');
        }

        return $this->render('ErpCoreBundle:crossBlocks:delete-confirmation-popup.html.twig', [
            'actionUrl' => $this->generateUrl('erp_user_fee_remove', ['id' => $id]),
        ]);
    }

    public function removeUserAction(User $user, Request $request)
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if ($currentUser->hasTenant($user)) {
            return $this->createNotFoundException();
        }

        if ($request->getMethod() === 'DELETE') {
            $user->clearFees();
            $rentPaymentBalance = $user->getRentPaymentBalance();
            $rentPaymentBalance->setBalance(0);

            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash(
                'alert_ok',
                'Success'
            );

            return $this->redirectToRoute('erp_user_dashboard_dashboard');
        }

        return $this->render('ErpCoreBundle:crossBlocks:delete-confirmation-popup.html.twig', [
            'actionUrl' => $this->generateUrl('erp_user_fee_remove_user', ['id' => $user->getId()]),
        ]);
    }

    /**
     * @param Fee $entity
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    private function update(Fee $entity, Request $request)
    {
        $form = $this->createForm(new FeeType(), $entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->em->persist($entity);
            $this->em->flush();

            $this->addFlash(
                'alert_ok',
                'Success'
            );

            return $this->redirect($this->generateUrl('erp_user_dashboard_dashboard'));
        }

        return $this->render('ErpUserBundle:Fee:form.html.twig', [
            'modalTitle' => 'Set Late Rent Payment Settings',
            'form' => $form->createView(),
        ]);
    }
}
