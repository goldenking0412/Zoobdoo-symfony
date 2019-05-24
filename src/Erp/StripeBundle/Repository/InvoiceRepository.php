<?php

namespace Erp\StripeBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Erp\PaymentBundle\Entity\StripeAccount;
use Erp\PaymentBundle\Entity\StripeCustomer;

class InvoiceRepository extends EntityRepository {

    /**
     * 
     * @param StripeAccount $stripeAccount
     * @param StripeCustomer[] $stripeCustomers
     * @param \DateTime $dateFrom
     * @param \DateTime $dateTo
     * @return type
     */
    public function getGroupedInvoices(StripeAccount $stripeAccount = null, $stripeCustomers = array(), \DateTime $dateFrom = null, \DateTime $dateTo = null) {
        $qb = $this->createQueryBuilder('i');
        $qb->select('COUNT(i.id) as gAmount, MONTH(i.created) as gMonth, YEAR(i.created) as gYear, CONCAT(YEAR(i.created), \'-\', MONTH(i.created)) as interval');

        if ($stripeAccount) {
            $qb->where('i.account = :account')
                    ->setParameter('account', $stripeAccount);
            if (!empty($stripeCustomers)) {
                $qb->orWhere($qb->expr()->in('i.customer', ':customer'))
                        ->setParameter('customer', $stripeCustomers);
            }
        }

        if ($dateFrom) {
            if ($dateTo) {
                $qb->andWhere($qb->expr()->between('i.created', ':dateFrom', ':dateTo'))
                        ->setParameter('dateTo', $dateTo);
            } else {
                $qb->andWhere('i.created > :dateFrom');
            }
            $qb->setParameter('dateFrom', $dateFrom);
        }

        $qb->groupBy('gYear')
                ->addGroupBy('gMonth');

        return $qb->getQuery()->getResult();
    }

    /**
     * 
     * @param StripeAccount $stripeAccount
     * @param StripeCustomer[] $stripeCustomers
     * @param \DateTime $dateFrom
     * @param \DateTime $dateTo
     * @return type
     */
    public function getInvoices(StripeAccount $stripeAccount = null, $stripeCustomers = array(), \DateTime $dateFrom = null, \DateTime $dateTo = null) {
        $qb = $this->createQueryBuilder('i')
                ->orderBy('i.created', 'DESC');

        if ($stripeAccount) {
            $qb->where('i.account = :account')
                    ->setParameter('account', $stripeAccount);
            if (!empty($stripeCustomers)) {
                $qb->orWhere($qb->expr()->in('i.customer', ':customer'))
                        ->setParameter('customer', $stripeCustomers);
            }
        }

        if ($dateFrom) {
            if ($dateTo) {
                $qb->andWhere($qb->expr()->between('i.created', ':dateFrom', ':dateTo'))
                        ->setParameter('dateTo', $dateTo);
            } else {
                $qb->andWhere('i.created > :dateFrom');
            }
            $qb->setParameter('dateFrom', $dateFrom);
        }

        return $qb->getQuery()->getResult();
    }

}
