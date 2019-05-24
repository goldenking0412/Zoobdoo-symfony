<?php

namespace Erp\UserBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Erp\UserBundle\Entity\User;
use Erp\StripeBundle\Entity\Transaction;
use Erp\PropertyBundle\Entity\PropertySecurityDeposit;

class StatementService {

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var \Erp\CoreBundle\Services\Logger
     */
    protected $logger;

    /**
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container = null) {
        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getManager();
        $this->logger = $this->container->get('erp.logger');
    }

    /**
     * 
     * @param User $manager
     * @param User $landlord
     * @param int $year
     * @param int $month
     * @return array
     */
    public function getRecordsForManagerStatement(User $manager, User $landlord, $year, $month) {
        if (
                $landlord->getStripeAccount()
                && $landlord->hasStripeCustomers()
                && $manager->getStripeAccount()
                && $manager->hasStripeCustomers()
        ) {
            $theAmountsOfTransactions = $this->em->getRepository(Transaction::class)
                    ->findRecordsForManagerStatement($manager, $landlord, $year, $month);
            $theAmountsOfPropertyDeposits = $this->em->getRepository(PropertySecurityDeposit::class)
                    ->findRecordsForManagerStatement($manager, $landlord, $year, $month);  
            
            $theRecords = array_merge($theAmountsOfTransactions, $theAmountsOfPropertyDeposits);
            
            usort($theRecords, function ($item1, $item2) {
                return new \DateTime($item1['dateCreated']) <=> new \DateTime($item2['dateCreated']);
            });

            return $theRecords;
        } else {
            return array();
        }
    }

    /**
     * 
     * @param User $user
     * @param int $year
     * @param int $month
     * @return array
     */
    public function getRecordsForStatement(User $user, $year, $month) {
        $theAmounts = array_merge(
                $this->em->getRepository(Transaction::class)->findRecordsForStatement($user, $year, $month),
                $this->em->getRepository(PropertySecurityDeposit::class)->findRecordsForStatement($user, $year, $month)
        );

        $incomesKeys = array(
            'rentPayments',
            'lateRentPayments',
            'invoices',
            'securityDepositsIncome'
        );

        $expensesKeys = array(
            'softwareFee',
            'securityDepositsExpenses',
            // 'feesAndCharges',
            'landlordPayouts',
            'tenantScreening'
        );

        $incomes = array();
        $expenses = array();
        foreach ($theAmounts as $key => $value) {
            if ($value < 0) {
                $value = -$value;
            }

            if (in_array($key, $incomesKeys)) {
                $incomes[$key] = $value;
            }

            if (in_array($key, $expensesKeys)) {
                $expenses[$key] = $value;
            }
        }

        return array(array_map('intval', $incomes), array_map('intval', $expenses));
    }

}
