<?php

namespace Erp\PropertyBundle\Repository;

use Erp\PropertyBundle\Entity\ContractForm;
use Doctrine\ORM\EntityRepository;

/**
 * ContractSectionRepository
 */
class ContractSectionRepository extends EntityRepository
{
    /**
     * Return sort number
     *
     * @param ContractForm $contractForm
     *
     * @return int
     */
    public function getSortNumber(ContractForm $contractForm)
    {
        $qb = $this->_em->createQueryBuilder()
            ->select('apps.sort')
            ->from($this->_entityName, 'apps')
            ->where('apps.contractForm = :form')
            ->setParameter('form', $contractForm)
            ->orderBy('apps.sort', 'DESC')
            ->setMaxResults(1)
        ;

        $result = $qb->getQuery()->getOneOrNullResult();

        if ($result) {
            $result['sort']++;
        } else {
            $result['sort'] = 1;
        }

        return $result['sort'];
    }
}
