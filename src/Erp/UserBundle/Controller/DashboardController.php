<?php

namespace Erp\UserBundle\Controller;

use Erp\CoreBundle\Controller\BaseController;
use Erp\PropertyBundle\Entity\PropertyRentHistory;
use Erp\PropertyBundle\Entity\Property;
use Erp\StripeBundle\Entity\Invoice;
use Erp\StripeBundle\Entity\Transaction;
use Erp\UserBundle\Entity\User;
use Erp\UserBundle\Entity\Fee;
use Stripe\BankAccount;
use Stripe\Card;
use Symfony\Component\HttpFoundation\Request;
use Erp\UserBundle\Form\Type\UserLateRentPaymentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

//TODO Refactor preparing chart data
class DashboardController extends BaseController {

    /**
     * @Security("is_granted('ROLE_MANAGER') or is_granted('ROLE_LANDLORD')")
     */
    public function dashboardAction() {
        /** @var User $user */
        $user = $this->getUser();
        return $this->render('ErpUserBundle:Dashboard:index.html.twig', [
                    'user' => $user,
        ]);
    }

    /**
     * @Security("is_granted('ROLE_MANAGER') or is_granted('ROLE_LANDLORD')")
     * 
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function showLateRentPaymentsAction() {
        /** @var User $user */
        $user = $this->getUser();
        $feeRepository = $this->em->getRepository(Fee::class);
        $userRepository = $this->em->getRepository(Property::class);

        $propertiesWasNotPaid = $userRepository->getDebtors($user);
        $fees = $feeRepository->getFees($user);

        $form = $this->createForm(new UserLateRentPaymentType());

        return $this->render('ErpUserBundle:Dashboard:late_rent_payments.html.twig', [
                    'properties_was_not_paid' => $propertiesWasNotPaid,
                    'fees' => $fees,
                    'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("is_granted('ROLE_MANAGER') or is_granted('ROLE_LANDLORD')")
     * 
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function showPropertiesAction() {
        /** @var User $user */
        $user = $this->getUser();

        $properties = ($user->hasRole(User::ROLE_MANAGER)) ? $user->getProperties() : $this->em->getRepository(Property::REPOSITORY)->findByLandlordUser($user);

        $availableProperties = 0;
        $rentedProperties = 0;
        foreach ($properties as $property) {
            switch ($property->getStatus()) {
                case Property::STATUS_DRAFT:
                case Property::STATUS_AVAILABLE:
                    ++$availableProperties;
                    break;
                case Property::STATUS_RENTED:
                    ++$rentedProperties;
                    break;
            }
        }

        return $this->render('ErpUserBundle:Dashboard:properties_history.html.twig', [
                    'available_properties' => $availableProperties,
                    'rented_properties' => $rentedProperties,
                    'labels' => array('Vacant', 'Rented')
        ]);
    }

    /**
     * @Security("is_granted('ROLE_MANAGER') or is_granted('ROLE_LANDLORD')")
     * 
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function showCashflowsAction() {
        /** @var User $user */
        $user = $this->getUser();

        $now = new \DateTime();
        $sixMonthsAgo = (new \DateTime())->modify('-5 month');
        $transactionRepo = $this->em->getRepository(Transaction::class);

        $labels = $this->getMonthsLabels($sixMonthsAgo, $now);
        $intervals = array_keys($labels);

        $cashOut = $this->getPreparedItems($transactionRepo->getGroupedTransactions(null, $user->getStripeCustomers(), $sixMonthsAgo, $now), $intervals);
        $cashIn = $this->getPreparedItems($transactionRepo->getGroupedTransactions($user->getStripeAccount(), array(), $sixMonthsAgo, $now), $intervals);

        return $this->render('ErpUserBundle:Dashboard:cashflows.html.twig', [
                    'cash_in' => $cashIn,
                    'cash_out' => $cashOut,
                    'labels' => array_values($labels),
                    'intervals' => $intervals,
        ]);
    }

    /**
     * @Security("is_granted('ROLE_MANAGER') or is_granted('ROLE_LANDLORD')")
     * 
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function showInvoicesAction() {
        /** @var User $user */
        $user = $this->getUser();

        $now = new \DateTime();
        $sixMonthsAgo = (new \DateTime())->modify('-5 month');
        
        $items = $this->em->getRepository(Invoice::class)->getGroupedInvoices($user->getStripeAccount(), $user->getStripeCustomers(), $sixMonthsAgo, $now);

        $labels = $this->getMonthsLabels($sixMonthsAgo, $now);
        $intervals = array_keys($labels);
        $labels = array_values($labels);
        $invoices = $this->getPreparedItems($items, $intervals);

        return $this->render('ErpUserBundle:Dashboard:invoices.html.twig', [
                    'labels' => $labels,
                    'invoices' => [],
                    'intervals' => $intervals,
        ]);
    }

    /**
     * @Security("is_granted('ROLE_MANAGER') or is_granted('ROLE_LANDLORD')")
     * 
     * @param Request $request
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function showTransactionsAction(Request $request) {
        /** @var User $user */
        $user = $this->getUser();
        $stripeAccount = $user->getStripeAccount();

        if (!$stripeAccount || $user->hasNoStripeCustomers()) {
            return $this->render('ErpUserBundle:Dashboard:transactions.html.twig', [
                        'pagination' => [],
            ]);
        }

        $repository = $this->em->getRepository(Transaction::class);
        $query = $repository->getTransactionsQuery($stripeAccount, $user->getStripeCustomers());

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($query, $request->query->getInt('page', 1));

        return $this->render('ErpUserBundle:Dashboard:transactions.html.twig', [
                    'pagination' => $pagination,
        ]);
    }

    /**
     * 
     * @return Response
     */
    public function showPaymentDetailsAction() {
        /** @var User $user */
        $user = $this->getUser();
        //TODO Add cache layer (APC or Doctrine)
        $stripeUserManager = $this->get('erp_stripe.stripe.entity.user_manager');
        /** @var BankAccount $bankAccount */
        $bankAccount = $stripeUserManager->getBankAccount($user);
        /** @var Card $creditCard */
        $creditCard = $stripeUserManager->getCreditCard($user);

        return $this->render('ErpPaymentBundle:Stripe/Widgets:payment-details.html.twig', [
                    'creditCard' => $creditCard,
                    'bankAccount' => $bankAccount,
        ]);
    }

    /**
     * 
     * @param \DateTime $dateFrom
     * @param \DateTime $dateTo
     * @return array
     */
    private function getMonthsLabels(\DateTime $dateFrom, \DateTime $dateTo) {
        $dateFrom = \DateTimeImmutable::createFromMutable($dateFrom);
        $dateTo = \DateTimeImmutable::createFromMutable($dateTo);

        $diff = $dateFrom->diff($dateTo);
        $count = ($diff->format('%y') * 12) + $diff->format('%m') + 1;

        $labels = [];
        for ($i = 1; $i <= $count; $i++) {
            $labels[$dateFrom->format('Y-n')] = $dateFrom->format('F');
            $dateFrom = $dateFrom->modify('+1 month');
        }

        return $labels;
    }

    /**
     * 
     * @param array $items
     * @param array $intervals
     * @return int
     */
    private function getPreparedItems(array $items, array $intervals) {
        //TODO Refactoring amount
        $results = [];
        $existingIntervals = array_column($items, 'interval');

        foreach ($intervals as $interval) {
            if (false !== $key = array_search($interval, $existingIntervals)) {
                $results[] = $items[$key]['gAmount'] / 100;
            } else {
                $results[] = 0;
            }
        }

        return $results;
    }

}
