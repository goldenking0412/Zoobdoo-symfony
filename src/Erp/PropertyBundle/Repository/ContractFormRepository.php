<?php

namespace Erp\PropertyBundle\Repository;

use Erp\UserBundle\Entity\User;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\EntityRepository;

/**
 * ContractFormRepository
 */
class ContractFormRepository extends EntityRepository
{
    /**
     * Return find prev form by user
     *
     * @param User $user
     *
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findPrevFormByUser(User $user)
    {
        $qb = $this->_em->createQueryBuilder()
            ->select('af')
            ->from($this->_entityName, 'af')
            ->join('af.property', 'p', Expr\Join::WITH, 'p.user = :user')
            ->setParameter('user', $user)
            ->orderBy('af.createdDate', 'DESC')
            ->setMaxResults(1)
        ;

        $result = $qb->getQuery()->getOneOrNullResult();

        return $result;
    }
}
