<?php

namespace Erp\PaymentBundle\Repository;

use Doctrine\ORM\EntityRepository;

class UnitSettingsRepository extends EntityRepository
{
    public function getSettings()
    {
        $qb = $this->createQueryBuilder('us');
        return $qb
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
