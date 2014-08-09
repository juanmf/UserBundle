<?php

namespace DocDigital\Bundle\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * RoleRepository Class
 *
 * @author Juan Manuel Fernandez <juanmf@gmail.com>
 */
class RoleRepository extends EntityRepository
{
    /**
     * Gets Roles withou parents i.e. Root Roles. 
     * 
     * @return array
     */
    public function getRootRoles()
    {
        return $this->createQueryBuilder('r')
            ->where('r.parent IS NULL')
            ->getQuery()
            ->execute();
    }
    
    /**
     * Prevents lots of lazy loading queries when making Role hierarchy.
     * 
     * @return array
     */
    public function findAllWithSons()
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.children', 'p')
            ->select('r, p')    
            ->getQuery()
            ->execute();
    }
}
