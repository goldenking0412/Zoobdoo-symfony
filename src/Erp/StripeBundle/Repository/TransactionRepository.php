<?php

namespace Erp\StripeBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Erp\PaymentBundle\Entity\StripeAccount;
use Erp\PaymentBundle\Entity\StripeCustomer;
use Erp\StripeBundle\Entity\Transaction;
use Erp\UserBundle\Entity\User;

class TransactionRepository extends EntityRepository {

    /**
     * 
     * @param StripeAccount $stripeAccount
     * @param StripeCustomer[] $stripeCustomers
     * @param \DateTime $dateFrom
     * @param \DateTime $dateTo
     * @return type
     */
    public function getGroupedTransactions(StripeAccount $stripeAccount = null, $stripeCustomers = array(), \DateTime $dateFrom = null, \DateTime $dateTo = null) {
        $qb = $this->createQueryBuilder('t');
        $qb->select('SUM(t.amount) as gAmount, MONTH(t.created) as gMonth, YEAR(t.created) as gYear, CONCAT(YEAR(t.created), \'-\', MONTH(t.created)) as interval');

        if ($stripeAccount) {
            $qb->where('t.account = :account')
                    ->setParameter('account', $stripeAccount);
        }

        if (!empty($stripeCustomers)) {
            $qb->orWhere($qb->expr()->in('t.customer', ':customer'))
                    ->setParameter('customer', $stripeCustomers);
        }

        if ($dateTo) {
            $dateTo->add(new \DateInterval('P1D')); // To include current date items also
        }
        if ($dateFrom) {
            if ($dateTo) {
                $qb->andWhere($qb->expr()->between('t.createdAt', ':dateFrom', ':dateTo'))
                        ->setParameter('dateTo', $dateTo);
            } else {
                $qb->andWhere('t.created > :dateFrom');
            }
            $qb->setParameter('dateFrom', $dateFrom);
        } elseif ($dateTo) {
            $qb->andWhere('t.created < :dateTo')
                    ->setParameter('dateTo', $dateTo);
        }

        $qb->groupBy('gYear')
                ->addGroupBy('gMonth');

        return $qb->getQuery()->getResult();
    }

    /**
     * 
     * @param StripeAccount $stripeAccount
     * @param StripeCustomer[]|array() $stripeCustomers
     * @param \DateTime $dateFrom
     * @param \DateTime $dateTo
     * @param type $type
     * @return type
     */
    public function getTransactionsQuery(StripeAccount $stripeAccount = null, $stripeCustomers = array(), \DateTime $dateFrom = null, \DateTime $dateTo = null, $type = null) {
        $qb = $this->createQueryBuilder('t')
                ->orderBy('t.createdAt', 'DESC');

        if ($stripeAccount) {
            $qb->where('t.account = :account')
                    ->setParameter('account', $stripeAccount);
            if (!empty($stripeCustomers)) {
                $qb->orWhere($qb->expr()->in('t.customer', ':customers'))
                        ->setParameter('customer', $stripeCustomers);
            }
        }

        if ($dateTo) {
            $dateTo->add(new \DateInterval('P1D')); // To include current date items also
        }
        if ($dateFrom) {
            if ($dateTo) {
                $qb->andWhere($qb->expr()->between('t.createdAt', ':dateFrom', ':dateTo'))
                        ->setParameter('dateTo', $dateTo);
            } else {
                $qb->andWhere('t.created > :dateFrom');
            }
            $qb->setParameter('dateFrom', $dateFrom);
        } elseif ($dateTo) {
            $qb->andWhere('t.created < :dateTo')
                    ->setParameter('dateTo', $dateTo);
        }

        if ($type) {
            $qb->andWhere(
                    $qb->expr()->in(
                            't.type', $type
                    )
            );
        }

        return $qb->getQuery();
    }

    /**
     * 
     * @param type $stripeAccountId
     * @param type $stripeCustomerId
     * @param \DateTime $dateFrom
     * @param \DateTime $dateTo
     * @param type $type
     * @param type $keywords
     * @return type
     */
    public function getTransactionsSearchQuery($stripeAccountId = null, $stripeCustomerId = null, \DateTime $dateFrom = null, \DateTime $dateTo = null, $type = null, $keywords = null) {
        $qb = $this->createQueryBuilder('t')
                ->orderBy('t.createdAt', 'DESC');
        $qb->leftJoin('ErpPaymentBundle:StripeAccount', 'sa', 'WITH', 'sa.id = t.account');
        $qb->leftJoin('ErpPaymentBundle:StripeCustomer', 'sc', 'WITH', 'sc.id = t.customer');
        $qb->leftJoin('ErpUserBundle:User', 'u', 'WITH', 'sc.user = u.id');
        if ($stripeAccountId) {  //outgoing transaction (account -> customer)
            $qb
                    ->andWhere(
                            $qb->expr()->in(
                                    't.account', $stripeAccountId
                            )
            );
            if ($stripeCustomerId) {
                $qb
                        ->andWhere(
                                $qb->expr()->in(
                                        't.customer', $stripeCustomerId
                                )
                );
            }
        }

        if ($dateTo) {
            $dateTo->add(new \DateInterval('P1D')); // To include current date items also
        }
        if ($dateFrom) {
            if ($dateTo) {
                $qb
                        ->andWhere($qb->expr()->between('t.createdAt', ':dateFrom', ':dateTo'))
                        ->setParameter('dateTo', $dateTo)
                ;
            } else {
                $qb->andWhere($qb->expr()->gt('t.createdAt', ':dateFrom'));
            }
            $qb->setParameter('dateFrom', $dateFrom);
        } elseif ($dateTo) {
            $qb
                    ->andWhere($qb->expr()->lt('t.createdAt', ':dateTo'))
                    ->setParameter('dateTo', $dateTo)
            ;
        }

        if ($type) {
            $qb->andWhere($qb->expr()->in('t.type', $type));
        }

        if ($keywords) {
            $words = explode(" ", $keywords);
            foreach ($words as $word) {
                $qb->andWhere(
                        $qb->expr()->orX(
                                $qb->expr()->like('u.firstName', ':word'),
                                $qb->expr()->like('u.lastName', ':word'),
                                $qb->expr()->like('t.metadata', ':word'),
                                $qb->expr()->like('t.status', ':word'),
                                $qb->expr()->like('t.internalType', ':word'),
                                $qb->expr()->like('t.amount', ':word')
                        )
                )->setParameter('word', '%' . $word . '%');
            }
        }

        return $qb->getQuery();
    }
    
    /**
     * 
     * @param \Erp\PropertyBundle\Entity\Property $property
     * @param \DateTime $dateFrom
     * @param \DateTime $dateTo
     * @param type $type
     * @param type $keywords
     * @return array
     */
    public function findTransactionsByProperty(\Erp\PropertyBundle\Entity\Property $property, \DateTime $dateFrom = null, \DateTime $dateTo = null, $type = null, $keywords = null) {
        $qb = $this->createQueryBuilder('t')->orderBy('t.createdAt', 'DESC');
        
        $qb->select('t.createdAt AS date, t.paymentMethodDescription AS paymentMethodDescription, t.internalType AS internalType')
                ->addSelect('t.metadata AS metadata, t.status AS status, t.amount AS amount, t.balance AS balance')
                ->addSelect('(CASE WHEN c.id IS NOT NULL THEN CONCAT(u.firstName, \' \', u.lastName) ELSE \'\' END) AS fullName')
                ->leftJoin('t.customer', 'c')
                ->leftJoin('c.user', 'u')
                ->where($qb->expr()->eq('t.property', ':property'))
                ->setParameter('property', $property)
        ;
        
        if ($dateTo) {
            $dateTo->add(new \DateInterval('P1D')); // To include current date items also
        }
        if ($dateFrom) {
            if ($dateTo) {
                $qb
                        ->andWhere($qb->expr()->between('t.createdAt', ':dateFrom', ':dateTo'))
                        ->setParameter('dateTo', $dateTo)
                ;
            } else {
                $qb->andWhere($qb->expr()->gt('t.created', ':dateFrom'));
            }
            $qb->setParameter('dateFrom', $dateFrom);
        } elseif ($dateTo) {
            $qb
                    ->andWhere($qb->expr()->lt('t.created', ':dateTo'))
                    ->setParameter('dateTo', $dateTo)
            ;
        }
        
        if ($type) {
            $qb->andWhere($qb->expr()->in('t.type', $type));
        }

        if ($keywords) {
            $words = explode(" ", $keywords);
            foreach ($words as $word) {
                $qb->andWhere(
                        $qb->expr()->orX(
                                $qb->expr()->like('u.firstName', ':word'),
                                $qb->expr()->like('u.lastName', ':word'),
                                $qb->expr()->like('t.metadata', ':word'),
                                $qb->expr()->like('t.status', ':word'),
                                $qb->expr()->like('t.internalType', ':word'),
                                $qb->expr()->like('t.amount', ':word')
                        )
                )->setParameter('word', '%' . $word . '%');
            }
        }
                
        return $qb->getQuery()->execute();
    }
    
    /**
     * 
     * @param User $manager
     * @param User $landlord
     * @param type $year
     * @param type $month
     * @return type
     */
    public function findRecordsForManagerStatement(User $manager, User $landlord, $year, $month) {
        $sqlIncome = $this->getSqlIncomesForManagerStatement($manager, $landlord, $year, $month);
        $sqlExpenses = $this->getSqlExpensesForManagerStatement($manager, $landlord, $sqlExpenses, $month);
        
        $sql = 'SELECT * FROM ((' . $sqlIncome . ') AS income), ((' . $sqlExpenses . ') AS expenses)';
        
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * 
     * @param \Erp\UserBundle\Entity\User $user
     * @param int $year
     * @param int $month
     * @return array
     */
    public function findRecordsForStatement(User $user, $year, $month = 0) {
        $em = $this->getEntityManager();
        $thisMetadata = $this->getClassMetadata();
        $stripeAccountMetadata = $em->getClassMetadata(StripeAccount::class);
        $stripeCustomerMetadata = $em->getClassMetadata(StripeCustomer::class);
        
        $transactionsTable = $thisMetadata->getTableName();
        $userTable = $em->getClassMetadata(User::class)->getTableName();
        $stripeAccountTable = $stripeAccountMetadata->getTableName();
        $stripeCustomerTable = $stripeCustomerMetadata->getTableName();
        
        $amountField = $thisMetadata->getColumnName('amount');
        $transactionTypeField = $thisMetadata->getColumnName('internalType');
        $createdAtField = $thisMetadata->getColumnName('createdAt');
        
        $sqlIncome = $this->getSqlIncomesForStatement($transactionTypeField, $amountField, $userTable,
                $stripeAccountTable, $transactionsTable, $createdAtField, $stripeAccountMetadata,
                $thisMetadata, $user, $year, $month);
        
        $sqlExpenses = $this->getSqlExpensesForStatement($transactionTypeField, $amountField, $userTable,
                $stripeCustomerTable, $transactionsTable, $createdAtField, $stripeCustomerMetadata,
                $thisMetadata, $user, $year, $month);
        
        $sql = 'SELECT * FROM ((' . $sqlIncome . ') AS income), ((' . $sqlExpenses . ') AS expenses)';
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll()[0];
    }
    
    /**
     * Remember that the User $u is registered within the ErpStripeBundle:Transaction:account
     * property (ErpPaymentBundle:StripeAccount object) when the same user receives
     * a payment.
     * Therefore, here I need the metadata and table involving the
     * ErpPaymentBundle:StripeAccount entity
     * 
     * @param string $transTypeField
     * @param string $sumField
     * @param string $uTable
     * @param string $stripeAccTable
     * @param string $transTable
     * @param string $dateField
     * @param ClassMetadata $stripeAccMetadata
     * @param ClassMetadata $thisMetadata
     * @param User $user
     * @param int $year
     * @param int $month
     * @return string
     */
    protected function getSqlIncomesForStatement($transTypeField, $sumField, $uTable, $stripeAccTable,
            $transTable, $dateField, ClassMetadata $stripeAccMetadata, ClassMetadata $thisMetadata,
            User $user, $year, $month = 0) {
        $sql = 'SELECT
                    COALESCE(SUM(CASE WHEN t.' . $transTypeField . ' = \'' . Transaction::INTERNAL_TYPE_RENT_PAYMENT . '\' THEN t.' . $sumField . ' END), 0) AS rentPayments,
                    COALESCE(SUM(CASE WHEN t.' . $transTypeField . ' = \'' . Transaction::INTERNAL_TYPE_LATE_RENT_PAYMENT . '\' THEN t.' . $sumField . ' END), 0) AS lateRentPayments,
                    COALESCE(SUM(CASE WHEN t.' . $transTypeField . ' = \'' . Transaction::INTERNAL_TYPE_CHARGE . '\' THEN t.' . $sumField . ' END), 0) AS invoices
                FROM ' . $uTable . ' u
                    INNER JOIN ' . $stripeAccTable . ' s ON u.id = s.' . $stripeAccMetadata->getAssociationMappings()['user']['joinColumns'][0]['name'] . '
                    INNER JOIN ' . $transTable . ' t ON s.id = t.' . $thisMetadata->getAssociationMappings()['account']['joinColumns'][0]['name'] . '
                WHERE u.id = ' . $user->getId() . '
                AND YEAR(t.' . $dateField . ') = ' . $year
        ;
        
        if ($month != 0) {
            $sql .= ' AND MONTH(t.' . $dateField . ') = ' . $month;
        }
        
        return $sql;
    }
    
    /**
     * Remember that the User $u is registered within the ErpStripeBundle:Transaction:customer
     * property (ErpPaymentBundle:StripeCustomer object) when the same user makes
     * a payment.
     * Therefore, here I need the metadata and table involving the
     * ErpPaymentBundle:StripeCustomer entity
     * 
     * @param string $transTypeField
     * @param string $sumField
     * @param string $uTable
     * @param string $stripeCustomerTable
     * @param string $transTable
     * @param string $dateField
     * @param ClassMetadata $stripeCustomerMetadata
     * @param ClassMetadata $thisMetadata
     * @param User $user
     * @param int $year
     * @param int $month
     * @return string
     */
    protected function getSqlExpensesForStatement($transTypeField, $sumField, $uTable, $stripeCustomerTable,
            $transTable, $dateField, ClassMetadata $stripeCustomerMetadata, ClassMetadata $thisMetadata,
            User $user, $year, $month = 0) {
        $sql = 'SELECT
                    COALESCE(SUM(CASE WHEN t.' . $transTypeField . ' = \'' . Transaction::INTERNAL_TYPE_TENANT_SCREENING . '\' THEN t.' . $sumField . ' END), 0) AS tenantScreening,
                    COALESCE(SUM(CASE WHEN t.' . $transTypeField . ' = \'' . Transaction::INTERNAL_TYPE_ANNUAL_SERVICE_FEE . '\' THEN t.' . $sumField . ' END), 0) AS softwareFee,
                    COALESCE(SUM(CASE WHEN t.' . $transTypeField . ' = \'' . Transaction::INTERNAL_TYPE_PAY_LANDLORD . '\' THEN t.' . $sumField . ' END), 0) AS landlordPayouts
                FROM ' . $uTable . ' u
                    INNER JOIN ' . $stripeCustomerTable . ' s ON u.id = s.' . $stripeCustomerMetadata->getAssociationMappings()['user']['joinColumns'][0]['name'] . '
                    INNER JOIN ' . $transTable . ' t ON s.id = t.' . $thisMetadata->getAssociationMappings()['account']['joinColumns'][0]['name'] . '
                WHERE u.id = ' . $user->getId() . '
                AND YEAR(t.' . $dateField . ') = ' . $year
        ;
        
        if ($month != 0) {
            $sql .= ' AND MONTH(t.' . $dateField . ') = ' . $month;
        }
        
        return $sql;
    }
    
    /**
     * Get the SQL for incomes, for detailed statements requested by the $manager
     * for the $landord.
     * Remember that, in this case, $manager receives money and then his/her
     * Stripe information are stored into ErpStripeBundle:Transaction:account
     * (ErpPaymentBundle:StripeAccount object) property, while the $landlord information
     * are collected within the ErpStripeBundle:Transaction:customer
     * (ErpPaymentBundle:StripeCustomer object) property.
     * 
     * @param User $manager
     * @param User $landlord
     * @param string $year
     * @param string $month
     * @return string the SQL string for incomes for the $manager from the $landlord
     */
    protected function getSqlIncomesForManagerStatement(User $manager, User $landlord, $year, $month) {
        $incomeTypes = array(
            Transaction::INTERNAL_TYPE_RENT_PAYMENT,
            Transaction::INTERNAL_TYPE_LATE_RENT_PAYMENT,
            Transaction::INTERNAL_TYPE_CHARGE
        );
        
        $qb = $this->createQueryBuilder('transaction');
        
        return $qb->select('transaction.createdAt AS dateCreated')
                ->addSelect($landlord->getFullName() . ' AS payer')
                ->addSelect($manager->getFullName() . ' AS payee')
                ->addSelect('transaction.stripeId AS id')
                ->addSelect('transaction.paymentMethodDescription AS description')
                ->addSelect('CAST(transaction.amount AS UNSIGNED) AS income')
                ->addSelect('0 as expense')
                ->join('transaction.account', 'stripeAccount')
                ->join('transaction.customer', 'stripeCustomer')
                ->where($qb->expr()->eq('stripeAccount.id', ':stripeAccountManager'))
                ->andWhere($qb->expr()->in('stripeCustomer.id', ':stripeCustomerLandlord'))
                ->andWhere($qb->expr()->eq('YEAR(transaction.createdAt'), ':year')
                ->andWhere($qb->expr()->eq('MONTH(transaction.createdAt'), ':month')
                ->andWhere($qb->expr()->in('transaction.internalType', ':incomeTypes'))
                ->setParameter('stripeAccountManager', $manager->getStripeAccount()->getId())
                ->setParameter('stripeCustomerLandlord', $landlord->getStripeCustomers())
                ->setParameter('year', $year)
                ->setParameter('month', $month)
                ->setParameter('incomeTypes', $incomeTypes)
                ->getQuery()
                ->getSQL()
        ;
    }
    
    /**
     * Get the SQL for expenses, for detailed statements requested by the $manager
     * for the $landord.
     * Remember that, in this case, $manager pays money and then his/her
     * Stripe information are stored into ErpStripeBundle:Transaction:customer
     * (ErpPaymentBundle:StripeCustomer object) property, while the $landlord information
     * are collected within the ErpStripeBundle:Transaction:account
     * (ErpPaymentBundle:StripeAccount object) property.
     * 
     * @param User $manager
     * @param User $landlord
     * @param string $year
     * @param string $month
     * @return string the SQL string for expenses
     */
    protected function getSqlExpensesForManagerStatement(User $manager, User $landlord, $year, $month) {
        $expenseTypes = array(
            Transaction::INTERNAL_TYPE_TENANT_SCREENING,
            Transaction::INTERNAL_TYPE_ANNUAL_SERVICE_FEE,
            Transaction::INTERNAL_TYPE_PAY_LANDLORD
        );
        
        $qb = $this->createQueryBuilder('transaction');
        
        return $qb->select('transaction.createdAt AS dateCreated')
                ->addSelect($manager->getFullName() . ' AS payer')
                ->addSelect($landlord->getFullName() . ' AS payee')
                ->addSelect('transaction.stripeId AS id')
                ->addSelect('transaction.paymentMethodDescription AS description')
                ->addSelect('0 as income')
                ->addSelect('CAST(transaction.amount AS UNSIGNED) AS expense')
                ->join('transaction.account', 'stripeAccount')
                ->join('transaction.customer', 'stripeCustomer')
                ->where($qb->expr()->eq('stripeAccount.id', ':stripeAccountLandlord'))
                ->andWhere($qb->expr()->in('stripeCustomer.id', ':stripeCustomerManagers'))
                ->andWhere($qb->expr()->eq('YEAR(transaction.createdAt'), ':year')
                ->andWhere($qb->expr()->eq('MONTH(transaction.createdAt'), ':month')
                ->andWhere($qb->expr()->in('transaction.internalType', ':expenseTypes'))
                ->setParameter('year', $year)
                ->setParameter('month', $month)
                ->setParameter('expenseTypes', $expenseTypes)
                ->setParameter('stripeAccountLandlord', $landlord->getStripeAccount()->getId())
                ->setParameter('stripeCustomerManagers', $manager->getStripeCustomers())
                ->getQuery()
                ->getSQL()
        ;
    }

}
