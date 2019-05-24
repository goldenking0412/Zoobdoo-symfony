<?php

namespace Erp\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr;
use Erp\UserBundle\Entity\User;
use Erp\PropertyBundle\Entity\Property;

/**
 * Class UserRepository
 *
 * @package Erp\UserBundle\Repository
 */
class UserRepository extends EntityRepository {

    /**
     * Get users by role
     *
     * @param string $role
     * @param string|null $otherRole
     *
     * @return array
     */
    public function findByRole($role, $otherRole = null) {
        $qb = $this->createQueryBuilder('u');
        $qb
                ->where($qb->expr()->like('u.roles', ':roles'))
                ->setParameter('roles', '%"' . $role . '"%')
        ;

        if ($otherRole) {
            $qb->orWhere('u.roles LIKE :otherRole')
                    ->setParameter('otherRole', '%"' . $otherRole . '"%');
        }
        
        $qb->orderBy('u.firstName')
                ->addOrderBy('u.lastName')
                ->addOrderBy('u.email')
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * Get users by roles
     *
     * @param mixed $roles
     * @return array
     */
    public function findByRoles($roles) {
        $qb = $this->createQueryBuilder('u');

        if (is_array($roles)) {
            $counter = 0;
            foreach ($roles as $role) {
                $qb->andWhere($qb->expr()->like('u.roles', ':role' . $counter))
                        ->setParameter('role' . $counter, '%"' . $role . '"%')
                ;
                
                $counter++;
            }
        } else {
            return $this->findByRole($roles);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * 
     * @param array $ids
     * @return User[]
     */
    public function findByArrayOfIds($ids) {
        $qb = $this->createQueryBuilder('u');

        return $qb->where($qb->expr()->in('u.id', ':ids'))
                        ->setParameter('ids', $ids)
                        ->getQuery()
                        ->getResult();
    }
    
    /**
     * 
     * @param User $landlord
     * @return User[]
     */
    public function findTenantsOfLandlord(User $landlord) {
        return $this
                ->getQueryFindTenantsOfLandlord($landlord)
                ->setParameter('landlord', $landlord)
                ->setParameter('statusDeleted', Property::STATUS_DELETED)
                ->getResult()
        ;
    }
    
    /**
     * 
     * @param User $landlord
     * @return User[]
     */
    public function findManagersOfLandlord(User $landlord) {
        return $this
                ->getQueryFindManagersOfLandlord($landlord)
                ->setParameter('landlord', $landlord)
                ->setParameter('statusDeleted', Property::STATUS_DELETED)
                ->getResult()
        ;
    }
    
    /**
     * 
     * @param User $landlord
     * @return User[]
     */
    public function findManagersAndTenantsOfLandlord(User $landlord) {
        $subDqlTenants = $this->getQueryFindTenantsOfLandlord($landlord, 'u1')->getDQL();
        $subDqlManagers = $this->getQueryFindManagersOfLandlord($landlord, 'u2')->getDQL();
        
        $qb = $this->createQueryBuilder('u');
        
        return $qb
                ->where($qb->expr()->in('u.id', $subDqlTenants))
                ->orWhere($qb->expr()->in('u.id', $subDqlManagers))
                ->addGroupBy('u.roles')
                ->setParameter('landlord', $landlord)
                ->setParameter('statusDeleted', Property::STATUS_DELETED)
                ->getQuery()
                ->getResult()
        ;
    }
    
    /**
     * 
     * @param User $landlord
     * @param string $subfixAlias
     * @return Query
     */
    protected function getQueryFindTenantsOfLandlord(User $landlord, $subfixAlias = null) {
        $aliasUser = 'u' . $subfixAlias;
        $aliasProperty = 'p' . $subfixAlias;
        $qb = $this->createQueryBuilder($aliasUser);
        
        return $qb
                ->join(Property::REPOSITORY, $aliasProperty, Expr\Join::WITH, $qb->expr()->eq($aliasUser . '.id', $aliasProperty . '.tenantUser'))
                ->where($qb->expr()->eq($aliasProperty . '.landlordUser', ':landlord'))
                ->andWhere($qb->expr()->neq($aliasProperty . '.status', ':statusDeleted'))
                ->getQuery()
        ;
    }
    
    /**
     * 
     * @param User $landlord
     * @param string $subfixAlias
     * @return Query
     */
    public function getQueryFindManagersOfLandlord(User $landlord, $subfixAlias = null) {
        $aliasUser = 'u' . $subfixAlias;
        $aliasProperty = 'p' . $subfixAlias;
        $qb = $this->createQueryBuilder($aliasUser);
        
        return $qb
                ->join(Property::REPOSITORY, $aliasProperty, Expr\Join::WITH, $qb->expr()->eq($aliasUser . '.id', $aliasProperty . '.user'))
                ->where($qb->expr()->eq($aliasProperty . '.landlordUser', ':landlord'))
                ->andWhere($qb->expr()->neq($aliasProperty . '.status', ':statusDeleted'))
                ->getQuery()
        ;
    }

}
