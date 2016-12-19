<?php

namespace DocDigital\Bundle\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * UserRepository Class
 *
 * @author Juan Manuel Fernandez <juanmf@gmail.com>
 */
class UserRepository extends EntityRepository
{
    /**
     * Overrides the default implementation of findOnBy() to prvent Roles lazy loading
     * 
     * FOS ends up calling this method from UserManager
     * 
     * {@inheritDoc}
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        $qb = $this->getQueryBuilder();
        foreach ($criteria as $fieldName => $value) {
            $qb->andWhere("u.$fieldName = :$fieldName")
                ->setParameter($fieldName, $value);
        }
        if ($orderBy) {
            foreach ($orderBy as $fieldName => $orientation) {
                $qb->addOrderBy("u.$fieldName", strtoupper(trim($orientation)));
            }
        }
        return $qb->getQuery()->getSingleResult();
    }
  
    /**
     * Gets users by role, if user has any of the given role or their ancestors.
     * 
     * @param string[]|string $roles one or many role names.
     * 
     * @return User[] having the rqeusted roles
     */
    public function findByAnyRoleName($rolesNames) 
    {
        $roles = $this->getRolesAndAncestors((array) $rolesNames);
        
        $qb = $this->getQueryBuilder();
        foreach ($roles as $k => $r) {
            $qb->orWhere("r = :role$k");
            $qb->setParameter("role$k", $r);
        }
        return $qb->getQuery()->execute();
    }

    /**
     * For each role givem retrieves the role and its ancestors.
     * 
     * @param String[] $rolesNames The role names.
     */
    public function getRolesAndAncestors($rolesNames) {
        $roleObjects = $this->getEntityManager()->getRepository('DdUserBundle:Role')->findAll();
        $matchedRolesObj = array_filter($roleObjects, function (Role $role) use ($rolesNames) {
            return in_array($role->getName(), $rolesNames);
        });
        
        $rolesAndAncestors = array();
        foreach ($matchedRolesObj as $role) {
            $rolesAndAncestors += $this->traverseForAncestors($role);
        }
        return $rolesAndAncestors;
    }

    public function traverseForAncestors($role) {
        $ancestors = array();
        while(null !== $role) {
            $ancestors[$role->getId()] = $role;
            $role = $role->getParent();
        }
        return $ancestors;
    }
    
    public function getQueryBuilder() 
    {
        $qb = $this->createQueryBuilder('u');
        $qb->leftJoin('u.roles', 'r');
        return $qb;
    }
}
