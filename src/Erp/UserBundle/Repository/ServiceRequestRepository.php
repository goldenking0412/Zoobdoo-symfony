<?php

namespace Erp\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Erp\UserBundle\Entity\User;

/**
 * Class ServiceRequest
 *
 * @package Erp\UserBundle\Repository
 */
class ServiceRequestRepository extends EntityRepository {
    
    /**
     * 
     * @param User $userTo
     * @return integer
     */
    public function findUnreadServiceRequestsByUserTo(User $userTo) {
        $qb = $this->createQueryBuilder('sr');
        
        return $qb->select('COUNT(sr)')
                ->where($qb->expr()->eq('sr.isRead', ':false'))
                ->andWhere($qb->expr()->eq('sr.toUser', ':toUser'))
                ->setParameter(':toUser', $userTo)
                ->setParameter(':false', false)
                ->getQuery()
                ->getSingleScalarResult()
        ;
    }

    /**
     * Return ServiceRequests
     *
     * @param User $user
     * @param User $toUser
     *
     * @return array
     */
    public function getServiceRequests(User $user, User $toUser) {
        $qb = $this->_em->createQueryBuilder()
                ->select('sr')
                ->from($this->_entityName, 'sr')
                ->where('sr.toUser IN (:users)')
                ->andWhere('sr.fromUser IN (:users)')
                ->setParameter('users', [$user, $toUser])
                ->addOrderBy('sr.createdDate', 'ASC')
        ;

        $result = $qb->getQuery()->getResult();

        return $result;
    }

    /**
     * Return tenants by manager and count messages
     *
     * @param User $user
     *
     * @return object
     */
    public function getTenantsByManager(User $user) {
        $qb = $this->_em->createQueryBuilder()
                ->select('sr, COUNT(sr) as totalServiceRequests')
                ->from($this->_entityName, 'sr')
                ->where('sr.toUser = :user')
                ->setParameter('user', $user)
                ->addGroupBy('sr.fromUser')
        ;

        $result = $qb->getQuery()->getResult();

        return $result;
    }

    /**
     * Return count messages for user
     *
     * @param User $fromUser
     * @param User $toUser
     *
     * @return int
     */
    public function getTotalMessagesByToUser(User $fromUser, User $toUser) {
        $qb = $this->_em->createQueryBuilder()
                ->select('COUNT(m)')
                ->from($this->_entityName, 'm')
                ->where('m.toUser IN (:users)')
                ->andWhere('m.fromUser IN (:users)')
                ->setParameter('users', [$fromUser, $toUser])
        ;

        $result = $qb->getQuery()->getSingleScalarResult();

        return $result;
    }

}
