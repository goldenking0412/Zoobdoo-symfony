<?php

namespace Erp\NotificationBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Erp\UserBundle\Entity\User;

class TemplateRepository extends EntityRepository {

    /**
     * @param User $user
     *
     * @return array
     */
    public function getTemplatesByUser(User $user) {
        $qb = $this->createQueryBuilder('t');
        $qb->select('t')
                ->join('t.user', 'u')
                ->where('u = :user')
                ->setParameter('user', $user);

        return $qb->getQuery()->getResult();
    }

    public function getTemplatesByUserIsSms(User $user, $isSms) {
        $qb = $this->createQueryBuilder('t');
        $qb->select('t')
                ->join('t.user', 'u')
                ->where('u = :user')
                ->andWhere('t.isSms = :isSms')
                ->setParameter('user', $user)
                ->setParameter('isSms', $isSms);

        return $qb->getQuery()->getResult();
    }

}
