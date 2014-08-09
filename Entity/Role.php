<?php

namespace DocDigital\Bundle\UserBundle\Entity;

use Symfony\Component\Security\Core\Role\RoleInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="fos_role")
 * @ORM\Entity(repositoryClass="DocDigital\Bundle\UserBundle\Entity\RoleRepository")
 * 
 * @see User
 * @see \DocDigital\Bundle\UserBundle\Role\RoleHierarchy
 * 
 * @author Juan Manuel Fernandez <juanmf@gmail.com>
 */
class Role implements RoleInterface
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="name", type="string", length=80, unique=true)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="Role", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true)
     * @var Role[]
     */
    private $parent;
    
    /**
     * @ORM\OneToMany(targetEntity="Role", mappedBy="parent")
     * @var ArrayCollection|Role[]
     */
    private $children;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="roles")
     */
    private $users;

    public function __construct($role = '')
    {
        if (0 !== strlen($role)) {
            $this->name = strtoupper($role);
        }
        $this->users = new ArrayCollection();
        $this->children = new ArrayCollection();
    }

    /**
     * @see RoleInterface
     */
    public function getRole()
    {
        return $this->name;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getUsers()
    {
        return $this->users;
    }

    public function addUser($user, $addRoleToUser = true)
    {
        $this->users->add($user);
        $addRoleToUser && $user->addRole($this, false);
    }

    public function removeUser($user)
    {
        $this->users->removeElement($user);
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function addChildren(Role $child, $setParentToChild = true)
    {
        $this->children->add($child);
        $setParentToChild && $child->setParent($this, false);
    }
    
    public function getDescendant(& $descendants = array())
    {
        foreach ($this->children as $role) {
            $descendants[spl_object_hash($role)] = $role;
            $role->getDescendant($descendants);
        }
        return $descendants;
    }
    
    public function removeChildren(Role $children)
    {
        $this->children->removeElement($children);
    }
    
    public function getParent()
    {
        return $this->parent;
    }

    public function setParent(Role $parent, $addChildToParent = true)
    {
        $addChildToParent && $parent->addChildren($this, false);
        $this->parent = $parent;
    }
    
    public function __toString()
    {
        if ($this->children->count()) {
            $childNameList = array();
            foreach ($this->children as $child) {
                $childNameList[] = $child->getName();
            }
            return sprintf('%s [%s]', $this->name, implode(', ', $childNameList));
        }
        return sprintf('%s', $this->name);
    }
}
