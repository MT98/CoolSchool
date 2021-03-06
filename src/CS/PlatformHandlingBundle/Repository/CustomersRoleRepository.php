<?php

namespace CS\PlatformHandlingBundle\Repository;

/**
 * CustomersRoleRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CustomersRoleRepository extends \Doctrine\ORM\EntityRepository
{
    public function removeAllRolesRelatedToService($id)
    {
        $qb = $this->_em->createQueryBuilder()->delete($this->_entityName,'a');
        $qb->where('a.service = :id');
        $qb->setParameter('id',$id);
        
        $qb->getQuery()->execute();
    }

    public function disableAllRolesRelatedToService($id)
    {
        $qb = $this->_em->createQueryBuilder()->update($this->_entityName,'a');
        $qb->set('a.isActive', false);
        $qb->where('a.service = :id');
        $qb->setParameter('id',$id);
        
        $qb->getQuery()->execute();
    }

    public function getRoleWithRelatedService($id)
    {
        $qb = $this->createQueryBuilder('a');
        $qb->leftJoin('a.service', 'ad')->addSelect('ad');
        

        $qb->where('a.id = :id');
        $qb->setParameter('id', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function getAllRolesWithRelatedServices()
    {
            $qb = $this->createQueryBuilder('a');
            $qb->leftJoin('a.service', 'ad')->addSelect('ad');
            $qb->orderBy('ad.name', 'asc');
            $qb->orderBy('a.name', 'asc');
            $qb->addOrderBy('a.code', 'asc');

            return $qb->getQuery()->getResult();
    }
}
