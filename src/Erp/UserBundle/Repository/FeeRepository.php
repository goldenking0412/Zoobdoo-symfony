<?php

namespace Erp\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Erp\UserBundle\Entity\User;

class FeeRepository extends EntityRepository
{
    public function getFees(User $user)
    {
        $qb = $this->createQueryBuilder('lrp')
            ->select('lrp, tu')
            ->join('lrp.user', 'tu')
            ->join('tu.tenantProperty', 'p')
            ->join('p.user', 'u')
            ->andWhere('p.user = :user')
            ->setParameter('user', $user);

        return $qb->getQuery()
            ->getResult();
    }
}