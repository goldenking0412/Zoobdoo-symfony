<?php

namespace Erp\StripeBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Erp\StripeBundle\Entity\Transaction;
use Erp\UserBundle\Entity\User;
use Erp\StripeBundle\Form\Type\TransactionsExportType;
use Erp\StripeBundle\Entity\TransactionsExport;
use Goodby\CSV\Export\Standard\ExporterConfig;
use Goodby\CSV\Export\Standard\Exporter;
use Goodby\CSV\Export\Standard\CsvFileObject;

class TransactionController extends Controller {

    /**
     * @Security("is_granted('ROLE_MANAGER')")
     */
    public function indexAction(Request $request) {
        /** @var User $user */
        $user = $this->getUser();
        $stripeAccount = $user->getStripeAccount();

        if (!$stripeAccount || $user->hasNoStripeCustomers()) {
            return $this->render('ErpStripeBundle:Transaction:index.html.twig', [
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
                ->getManagerForClass(Transaction::class)
                ->getRepository(Transaction::class)
                ->getTransactionsQuery($user->getStripeAccount(), $user->getStripeCustomer(), $dateFrom, $dateTo)
        ;

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query, $request->query->getInt('page', 1)
        );

        return $this->render('ErpStripeBundle:Transaction:index.html.twig', [
                    'pagination' => $pagination,
        ]);
    }

    /**
     * @Security("is_granted('ROLE_MANAGER')")
     */
    public function exportAction(Request $request) {
        /** @var User $user */
        $user = $this->getUser();
        $stripeAccount = $user->getStripeAccount();

        $transactionsExport = new TransactionsExport();
        $form = $this->createForm(new TransactionsExportType(), $transactionsExport);
        $form->handleRequest($request);

        if (!$stripeAccount || $user->hasNoStripeCustomers()) {
            return $this->render('ErpStripeBundle:Transaction:export_form.html.twig', [
                        'form' => $form->createView(),
            ]);
        }
        if ($form->isSubmitted()) {

            if ($form->isValid()) {
                $dateFrom = $transactionsExport->getDateFrom();
                $dateTo = $transactionsExport->getDateTo();

                $repository = $this->getDoctrine()->getManagerForClass(Transaction::class)->getRepository(Transaction::class);
                $transactionsQuery = $repository->getTransactionsQuery($stripeAccount, $user->getStripeCustomer(), $dateFrom, $dateTo);
                $transactions = $transactionsQuery->getResult();

                $filename = 'Transaction_history';
                //TODO Refactoring. Strategy form CSV/PDF. Decorator Objects or array
                if ($form->get('pdf_submit')->isClicked()) {
                    $html = $this->renderView('ErpStripeBundle:Transaction:export.html.twig', [
                        'transactions' => $transactions,
                    ]);
                    $dompdf = $this->get('slik_dompdf');
                    $dompdf->getpdf($html);
                    $dompdf->stream(sprintf('%s.pdf', $filename));

                    return new Response(
                            $dompdf->output(), 200, [
                        'Content-Type' => 'application/pdf',
                        'Content-Disposition' => sprintf('attachment; filename="%s.pdf"', $filename),
                            ]
                    );
                } elseif ($form->get('csv_submit')->isClicked()) {
                    $i = 0;
                    $items = [];
                    /** @var Transaction $transaction */
                    foreach ($transactions as $transaction) {
                        $items[] = [
                            $i++,
                            $transaction->getCreated()->format('Y/m/d'),
                            $transaction->getAmount(),
                            $transaction->getCurrency(),
                        ];
                    }

                    $response = new StreamedResponse();
                    $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
                    $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s.csv"', $filename));
                    $response->setCallback(
                            function () use ($items) {
                        $config = new ExporterConfig();
                        $config->setDelimiter(';')->setToCharset('UTF-8')->setFileMode(CsvFileObject::FILE_MODE_WRITE);
                        $exporter = new Exporter($config);
                        $exporter->export('php://output', $items);
                    }
                    );

                    return $response;
                }
            } else {
                //TODO Handle an errors
            }
        }

        return $this->render('ErpStripeBundle:Transaction:export_form.html.twig', [
                    'form' => $form->createView(),
        ]);
    }

}
