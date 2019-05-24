<?php

namespace Erp\UserBundle\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\EntityRepository;
use Erp\PropertyBundle\Entity\Property;
use Erp\UserBundle\Entity\Remittance;

/**
 * Class RemittanceRepository
 *
 * @package Erp\UserBundle\Repository
 */
class RemittanceRepository extends EntityRepository {

    /**
     * 
     * @param \DateTime $dateFrom
     * @param \DateTime $dateTo
     * @param Property $property
     * @param string $type
     * @param string $keywords
     * @return Query
     */
    public function getRemittancesSearchQuery(\DateTime $dateFrom = null, \DateTime $dateTo = null, Property $property = null, $type = null, $keywords = null) {
        $qb = $this->createQueryBuilder('r')->orderBy('r.createdAt', 'DESC');

        $qb
                ->join('r.toUser', 'u')
                ->join('r.property', 'p')
        ;

        if ($property) {
            $qb->andWhere($qb->expr()->in('p', $property));
        }

        if ($dateFrom) {
            if ($dateTo) {
                $qb
                        ->andWhere($qb->expr()->between('r.createdAt', ':dateFrom', ':dateTo'))
                        ->setParameter('dateTo', $dateTo)
                ;
            } else {
                $qb->andWhere($qb->expr()->gte('r.createdAt', ':dateFrom'));
            }
            $qb->setParameter('dateFrom', $dateFrom);
        } else {
            if ($dateTo) {
                $qb
                        ->andWhere($qb->expr()->lte('r.createdAt', ':dateTo'))
                        ->setParameter('dateTo', $dateTo)
                ;
            }
        }

        if ($type) {
            $types = Remittance::getTypeOptions();
            
            $qb
                    ->andWhere($qb->expr()->eq('r.type', ':type'))
                    ->setParameter('type', $types[$type])
            ;
        }

        if ($keywords) {
            $words = explode(" ", $keywords);
            foreach ($words as $word) {
                $qb
                        ->andWhere(
                                $qb->expr()->orX(
                                        $qb->expr()->like('u.firstName', ':word'),
                                        $qb->expr()->like('u.lastName', ':word'),
                                        $qb->expr()->like('r.amount', ':word'),
                                        $qb->expr()->like('r.comment', ':word')
                                )
                        )
                        ->setParameter('word', '%' . $word . '%')
                ;
            }
        }

        return $qb->getQuery();
    }

}
