<?php

namespace DocDigital\Bundle\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;
use DocDigital\Bundle\ProcessBundle\Util\Role\Role as ProcessUtilRole;

/**
 * Description of RoleRepository
 *
 * @author Juan Manuel Fernandez <juanmf@gmail.com>
 */
class UserRepository extends EntityRepository
{
    public function getRootRoles()
    {
        return $this->createQueryBuilder('r')
            ->where('r.parent IS NULL')
            ->getQuery()->execute();
    }
    
}
