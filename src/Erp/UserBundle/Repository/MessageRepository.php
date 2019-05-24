<?php

namespace Erp\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Erp\UserBundle\Entity\User;

/**
 * Class Message
 *
 * @package Erp\UserBundle\Repository
 */
class MessageRepository extends EntityRepository {

    /**
     * Return messages for user
     *
     * @param User $user
     * @param User $toUser
     *
     * @return array
     */
    public function getMessages(User $user, User $toUser) {
        $qb = $this->createQueryBuilder('m');

        $qb
                ->where('m.toUser IN (:users)')
                ->andWhere('m.fromUser IN (:users)')
                ->setParameter('users', [$user, $toUser])
                ->addOrderBy('m.createdDate', 'DESC')
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * Return unread messages
     *
     * @param User $user
     * @param User|null $fromUser
     *
     * @return Message[]
     */
    public function getUnreadMessages(User $user, User $fromUser = null) {
        $qb = $this->createQueryBuilder('m');

        $qb
                ->where($qb->expr()->eq('m.toUser', ':toUser'))
                ->andWhere($qb->expr()->eq('m.isRead', ':isRead'))
                ->setParameter('toUser', $user)
                ->setParameter('isRead', 0)
        ;

        if ($fromUser) {
            $qb->andWhere($qb->expr()->eq('m.fromUser', ':fromUser'))->setParameter('fromUser', $fromUser);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Return count unread messages
     *
     * @param User $user
     * @param User|null $fromUser
     *
     * @return int
     */
    public function getCountUnreadMessages(User $user, User $fromUser = null) {
        $qb = $this->createQueryBuilder('m');
        
        $qb
                ->select('COUNT(m)')
                ->where('m.toUser = :toUser')
                ->andWhere('m.isRead = :isRead')
                ->setParameter('toUser', $user)
                ->setParameter('isRead', 0)
        ;

        if ($fromUser) {
            $qb->andWhere('m.fromUser = :fromUser')->setParameter('fromUser', $fromUser);
        }

        return $qb->getQuery()->getSingleScalarResult();
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
        $qb = $this->createQueryBuilder('m');

        $qb
                ->select('COUNT(m)')
                ->where('m.toUser IN (:users)')
                ->andWhere('m.fromUser IN (:users)')
                ->setParameter('users', [$fromUser, $toUser])
        ;

        return $qb->getQuery()->getSingleScalarResult();
    }

}
