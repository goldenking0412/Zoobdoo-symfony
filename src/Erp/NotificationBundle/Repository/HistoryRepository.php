<?php

namespace Erp\NotificationBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Erp\UserBundle\Entity\User;

class HistoryRepository extends EntityRepository {

    public function getHistoryByUser(User $user) {
        $qb = $this->createQueryBuilder('h');

        return $qb->select('h')
                        ->join('h.property', 'p')
                        ->where($qb->expr()->eq('p.user', ':user'))
                        ->setParameter('user', $user)
                        ->orderBy('h.createdAt', 'DESC')
                        ->getQuery()
                        ->getResult()
        ;
    }

}
