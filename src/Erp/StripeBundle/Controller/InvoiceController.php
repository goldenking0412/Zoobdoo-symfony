<?php

namespace Erp\StripeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Erp\StripeBundle\Entity\Invoice;
use Erp\UserBundle\Entity\User;

class InvoiceController extends Controller {

    public function indexAction(Request $request) {
        /** @var User $user */
        $user = $this->getUser();
        $stripeAccount = $user->getStripeAccount();

        if (!$stripeAccount || $user->hasNoStripeCustomers()) {
            return $this->render('ErpStripeBundle:Invoice:index.html.twig', [
                        'user' => $user,
                        'error' => 'Please, verify you bank account.'
            ]);
        }
        //TODO Do more flexible. Create filter model, form
        $interval = $request->query->get('filter[interval]', null, true);

        $dateFrom = \DateTimeImmutable::createFromFormat('Y-n', $interval)->modify('first day of this month')->setTime(0, 0, 0);
        $dateTo = $dateFrom->modify('+1 month');

        $dateFrom = (new \DateTime())->setTimestamp($dateFrom->getTimestamp());
        $dateTo = (new \DateTime())->setTimestamp($dateTo->getTimestamp());

        $query = $this
                ->getDoctrine()
                ->getManagerForClass(Invoice::class)
                ->getRepository(Invoice::class)
                ->getInvoices($stripeAccount, $user->getStripeCustomers(), $dateFrom, $dateTo)
        ;

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query, $request->query->getInt('page', 1)
        );

        return $this->render('ErpStripeBundle:Invoice:index.html.twig', [
                    'user' => $user,
                    'pagination' => $pagination,
        ]);
    }

}
