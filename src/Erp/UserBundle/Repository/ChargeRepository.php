<?php

namespace Erp\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Erp\UserBundle\Entity\User;
use Erp\UserBundle\Entity\Charge;
use Doctrine\ORM\Query;

class ChargeRepository extends EntityRepository {

    /**
     * 
     * @param User $manager
     * @param \DateTime $from | null
     * @param \DateTime $to | null
     * @return @return \Erp\UserBundle\Entity\Charge[]
     */
    public function findChargesByManager(User $manager, \DateTime $from = null, \DateTime $to = null) {
        return $this->getQueryChargesByManager($manager, $from, $to)->getResult();
    }

    /**
     * 
     * @param User $manager
     * @param \DateTime $from
     * @param \DateTime $to
     * @return integer
     */
    public function findCountChargesByManager(User $manager, \DateTime $from = null, \DateTime $to = null) {
        $this->getQueryCountChargesByManager($manager, $from, $to)->getSingleScalarResult();
    }

    /**
     * 
     * @param \DateTime $date | null
     * @param integer $payment
     * @param integer $dividedBy
     * @return \Erp\UserBundle\Entity\Charge[]
     */
    public function findUnpaidCharges(\DateTime $date = null, $payment = 30, $dividedBy = 3) {
        $qb = $this->createQueryBuilder('c');

        $qb->where($qb->expr()->neq('c.status', ':paid'))
                ->setParameter('paid', Charge::STATUS_PAID);

        if ($date && $payment && $dividedBy) {
            $qb
                    ->andWhere($qb->expr()->andX(
                                    $qb->expr()->gte(':date', 'DATE_ADD(c.createdAt, INTERVAL :payment DAY)'), $qb->expr()->eq('MOD(DATE_DIFF(:date, c.createdAt), :dividedBy)', 0)
                    ))
                    ->setParameter('payment', $payment)
                    ->setParameter('date', $date)
                    ->setParameter('dividedBy', $dividedBy);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * 
     * Get the recurrent charges even considering that the months do not end
     * with the same day-number (e.g., last day of February is 28, unless for
     * leap years).
     * In this way, the function checks if the next day of the
     * $theDate date passed is equal to 1.
     * If it is, then all the charges with recurringDayOfMonth >= day($theDate)
     * are considered. Otherwise, selection is carried out just on = formula
     * 
     * @param \DateTime $theDate
     * @return \Erp\UserBundle\Entity\Charge[]
     */
    public function findRecurringCharges(\DateTime $theDate) {
        $theDay = $theDate->format('j');
        $theNextDay = $theDate->add(new \DateInterval('P1D'))->format('j');

        $qb = $this->createQueryBuilder('c');

        if ($theNextDay == 1) {
            $qb->where($qb->expr()->gte('c.recurringDayOfMonth', ':theDay'));
        } else {
            $qb->where($qb->expr()->eq('c.recurringDayOfMonth', ':theDay'));
        }

        return $qb->setParameter('theDay', $theDay)->getQuery()->getResult();
    }

    /**
     * 
     * @param User $manager
     * @param \DateTime $from | null
     * @param \DateTime $to | null
     * @return Query
     */
    public function getQueryChargesByManager(User $manager, \DateTime $from = null, \DateTime $to = null) {
        $qb = $this->createQueryBuilder('c');

        $qb->where($qb->expr()->eq('c.manager', ':manager'))
                ->setParameter('manager', $manager)
        ;

        if ($from) {
            $qb->andWhere($qb->expr()->gte('c.createdAt', ':from'))
                    ->setParameter('from', $from);
        }

        if ($to) {
            $qb->andWhere($qb->expr()->lte('c.createdAt', ':to'))
                    ->setParameter('to', $to);
        }

        return $qb->getQuery();
    }

    /**
     * 
     * @param User $manager
     * @param \DateTime $from | null
     * @param \DateTime $to | null
     * @return Query
     */
    public function getQueryCountChargesByManager(User $manager, \DateTime $from = null, \DateTime $to = null) {
        $qb = $this->createQueryBuilder('c');

        $qb->select('COUNT(c)')
                ->where($qb->expr()->eq('c.manager', ':manager'))
                ->setParameter('manager', $manager);



        if ($from) {
            $qb->andWhere($qb->expr()->gte('c.createdAt', ':from'))
                    ->setParameter('from', $from);
        }

        if ($to) {
            $qb->andWhere($qb->expr()->lte('c.createdAt', ':to'))
                    ->setParameter('to', $to);
        }

        return $qb->getQuery();
    }

}
