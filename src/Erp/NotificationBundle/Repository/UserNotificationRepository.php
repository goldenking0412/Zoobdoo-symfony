<?php

namespace Erp\NotificationBundle\Repository;

use Erp\PropertyBundle\Entity\Property;
use Erp\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

class UserNotificationRepository extends EntityRepository {

    /**
     * @param $id
     * @param User $user
     *
     * @return array
     */
    public function getAlertByUserAndId($id, User $user) {
        $qb = $this->getAlertByUserQuery($user);

        return $qb->andWhere('un.id = :id')
                        ->setParameter('id', $id)
                        ->setMaxResults(1)
                        ->getQuery()
                        ->getOneOrNullResult()
        ;
    }

    /**
     * @param User $user
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getAlertByUserQuery(User $user) {
        $qb = $this->createQueryBuilder('un');

        return $qb->select('un')
                        ->join('un.properties', 'p')
                        ->where('p.user = :user')
                        ->setParameter('user', $user)
                        ->andWhere('p.status != :status')
                        ->setParameter('status', Property::STATUS_DELETED)
        ;
    }

    public function getAlertsByUser(User $user) {
        return $this->getAlertByUserQuery($user)
                        ->addOrderBy('un.id', 'DESC')
                        ->getQuery()->getResult();
    }

    public function getPropertiesFromUserNotificationIterator() {
        return $this->createQueryBuilder('un')
                        ->distinct()
                        ->select('un.id AS userNotificationId')
                        ->addSelect('un.sendAlertAutomatically')
                        ->addSelect('un.sendNotificationAutomatically')
                        ->addSelect('p.id AS propertyId')
                        ->addSelect('t.id as templateId')
                        ->addSelect('t.type as type')
                        ->addSelect('t.title as title')
                        ->join('un.template', 't')
                        ->join('un.properties', 'p')
                        ->join('p.settings', 'ps', 'WITH', 'ps.dayUntilDue IS NOT NULL')
                        ->andWhere('p.tenantUser IS NOT NULL')
                        ->andWhere('p.status != :status')
                        ->setParameter('status', Property::STATUS_DELETED)
        ;
    }

    public function getPropertiesFromNotificationsIterator() {
        $qb = $this->getPropertiesFromUserNotificationIterator();
        return $qb->leftJoin('un.notifications', 'n', 'WITH', '(ps.dayUntilDue - DAY(CURRENT_DATE())) = n.daysBefore')
                        // TODO: refactor this to more `doctrine` way
                        ->andWhere($qb->expr()->orX(
                                        $qb->expr()->andX(
                                                $qb->expr()->isNull('n.id'),
                                                $qb->expr()->eq('un.sendNotificationAutomatically', '1'),
                                                $qb->expr()->eq('ps.dayUntilDue', 'DAY(CURRENT_DATE())')
                                        )
                                ), $qb->expr()->andX(
                                        $qb->expr()->isNotNull('n.id'),
                                        $qb->expr()->eq('un.sendNotificationAutomatically', '0')
                                )
                        )
                        ->addSelect('n.id AS notificationId')
                        ->addSelect('(ABS(ps.dayUntilDue - DAY(CURRENT_DATE()))) AS calculatedDaysBefore')
                        ->addSelect('0 AS calculatedDaysAfter')
                        ->getQuery()->iterate()
        ;
    }

    public function getPropertiesFromAlertsIterator() {
        return $this->getPropertiesFromUserNotificationIterator()
//            ->join('un.alerts', 'a', 'WITH', '(DAY(CURRENT_DATE()) - ps.dayUntilDue) = a.daysAfter')
                        ->andWhere('p.paidDate IS NULL OR DATE_ADD(p.paidDate, 1, \'MONTH\') < CURRENT_DATE()')
                        ->addSelect('a.id AS alertId')
                        ->addSelect('(ABS(DAY(CURRENT_DATE()) - ps.dayUntilDue)) AS calculatedDaysAfter')
                        ->addSelect('0 AS calculatedDaysBefore')
                        ->join('un.alerts', 'a', 'WITH', 'a.userNotification=un.id')
                        ->getQuery()->iterate()
        ;
    }

}
