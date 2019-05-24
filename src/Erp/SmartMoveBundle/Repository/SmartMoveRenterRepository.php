<?php

namespace Erp\SmartMoveBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Erp\UserBundle\Entity\User;

/**
 * Class SmartMoveRenterRepository
 *
 * @package Erp\SmartMoveBundle\Repository
 */
class SmartMoveRenterRepository extends EntityRepository
{
    /**
     * Get one by manager with smPropertyId and smApplicationId
     *
     * @param User $user
     *
     * @return SmartMoveRenter|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getByManager(User $user)
    {
        $qb = $this->_em->createQueryBuilder()
            ->select('sm_r')
            ->from($this->_entityName, 'sm_r')
            ->where('sm_r.manager = :manager')
            ->andWhere('sm_r.smPropertyId IS NOT NULL')
            ->andWhere('sm_r.smApplicationId IS NOT NULL')
            ->orderBy('sm_r.id', 'DESC')
            ->setParameter('manager', $user)
            ->setMaxResults(1);
        $result = $qb->getQuery()->getOneOrNullResult();

        return $result;
    }
}
