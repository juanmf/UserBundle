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
        $qb = $this->createQueryBuilder('u');
        $qb->leftJoin('u.roles', 'r');
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
}
