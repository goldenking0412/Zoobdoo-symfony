<?php

namespace Erp\StripeBundle\Repository;

use Doctrine\ORM\EntityRepository;

class SubscriptionRepository extends EntityRepository
{
    public function getSubscriptionsByEndOfTrialPeriod(\DateTime $date)
    {
        $qb = $this->createQueryBuilder('s');

        $qb->select('s')
            ->join('s.stripeCustomer', 'c')
            ->join('c.user', 'u')
            ->where('s.trialPeriodStartAt = :date')
            ->setParameter('date', $date);

        return $qb->getQuery()
            ->getResult();
    }
}
