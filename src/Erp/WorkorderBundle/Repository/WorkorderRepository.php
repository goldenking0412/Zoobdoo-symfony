<?php

namespace Erp\WorkorderBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Erp\UserBundle\Entity\User;

/**
 * 
 */
class WorkorderRepository Extends EntityRepository {
    
    /**
     * 
     * @param User $manager
     * @return integer
     */
    public function findCountUncompletedWorkOrdersByManager(User $manager) {
        $qb = $this->createQueryBuilder('w');
        
        return $qb->select('COUNT(w.id)')
                ->where($qb->expr()->neq('w.status', ':completed'))
                ->andWhere($qb->expr()->eq('w.manager', ':manager'))
                ->setParameter('completed', \Erp\WorkorderBundle\Entity\Workorder::STATUS_COMPLETED)
                ->setParameter('manager', $manager)
                ->getQuery()
                ->getSingleScalarResult()
        ;
    }

    public function getWorkOrderQuery($managerId = null) {
        $qb = $this->createQueryBuilder('wo');

        if (!(is_null($managerId))) {
            $qb
                    ->where($qb->expr()->eq('wo.manager', ':managerId'))
                    ->setParameter('managerId', $managerId)
            ;
        }

        return $qb->getQuery();
    }

}
