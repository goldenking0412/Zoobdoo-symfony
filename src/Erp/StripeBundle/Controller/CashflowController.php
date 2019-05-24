<?php

namespace Erp\StripeBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Erp\StripeBundle\Entity\Transaction;
use Erp\StripeBundle\Form\Type\CashflowFilterType;
use Erp\UserBundle\Entity\User;
use Erp\StripeBundle\Guesser\TransactionTypeGuesser;

class CashflowController extends Controller {

    /**
     * @Security("is_granted('ROLE_MANAGER')")
     */
    public function indexAction(Request $request) {
        /** @var User $user */
        $user = $this->getUser();
        $stripeAccount = $user->getStripeAccount();

        if (!$stripeAccount || $user->hasNoStripeCustomers()) {
            $this->addFlash('alert_error', 'Please, verify your bank account.');
            $this->addFlash('show_navigation_sign_after_register', ''); //popover for Verify button
            return $this->redirect($this->generateUrl('erp_user_dashboard_dashboard'));
        }

        $form = $this->createForm(new CashflowFilterType());
        $form->handleRequest($request);

        $data = $form->getData();
        $transactionTypeGuesser = new TransactionTypeGuesser();
        $guessedType = $transactionTypeGuesser->guess($data['type']);
        $query = $this
                ->getDoctrine()
                ->getManagerForClass(Transaction::class)
                ->getRepository(Transaction::class)
                ->getTransactionsQuery($stripeAccount, $user->getStripeCustomers(), $data['dateFrom'], $data['dateTo'], $guessedType)
        ;

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($query, $request->query->getInt('page', 1));
        return $this->render('ErpStripeBundle:Cashflow:index.html.twig', [
                    'pagination' => $pagination,
                    'type' => $data['type'],
                    'date_from' => $data['dateFrom']
        ]);
    }

}
