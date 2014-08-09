<?php

namespace DocDigital\Bundle\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Security\Core\Role\RoleInterface;

/**
 * @ORM\Entity(repositoryClass="DocDigital\Bundle\UserBundle\Entity\UserRepository")
 * 
 * @ORM\Table(name="fos_user")
 * @Assert\Callback(methods={"validatePassword"})
 */
class User extends BaseUser
{
    use TimestampableEntity;
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="apellido", type="string", length=255, nullable=true)
     * @Assert\NotBlank
     */
    private $apellido;

    /**
     * @var string
     * @ORM\Column(name="nombre", type="string", length=255, nullable=true)
     * @Assert\NotBlank
     */
    private $nombre;
    
    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Role", inversedBy="users", cascade={"persist"})
     * @ORM\JoinTable(name="fos_user_role")
     */
    protected $roles;
    
    public function __construct()
    {
        parent::__construct();
        $this->roles = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set apellido
     *
     * @param string $apellido
     * @return User
     */
    public function setApellido($apellido)
    {
        $this->apellido = $apellido;

        return $this;
    }

    /**
     * Get apellido
     *
     * @return string 
     */
    public function getApellido()
    {
        return $this->apellido;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return User
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Si es un user nuevo el password debe ser obligatorio.
     *
     * @param ExecutionContextInterface $ec Contexto de Ejecuci..n.
     *
     * @return void
     */
    public function validatePassword(ExecutionContextInterface $ec)
    {
        $plainPassword = $this->getPlainPassword();
        if (!$this->id && empty($plainPassword)) {
            $ec->addViolationAt('plainPassword', 'La contraseña no debe estar vacía.');
        }
    }
    
    /**
     * Added for entityType forms nor handling string[] Roles
     * 
     * @return type
     */
    public function getRolesCollection()
    {
        return $this->roles;
    }
    
    /**
     * Added for entityType forms nor handling string[] Roles
     * 
     * @param type $role
     * @return type
     */
    public function addRoleCollection($role)
    {
        return $this->addRole($role);
    }
    
    /**
     * Added for entityType forms nor handling string[] Roles
     * 
     * @param type $role
     * @return type
     */
    public function removeRoleCollection($role)
    {
        return $this->removeRole($role);
    }
    
    public function getRoles()
    {
        if (! $this->roles->count()) {
            return array(parent::ROLE_DEFAULT);
        }
        $roles = $this->roles->toArray();
        foreach ($this->getGroups() as $group) {
            $roles = array_merge($roles, $group->getRoles());
        }
        foreach ($roles as $k => $role) {
            /* 
             * Ensure String[] to prevent bad unserialized UsernamePasswordToken with for instance 
             * UPT#roles:{Role('ROLE_USER'), 'ROLE_USER'} which ends in Error: Call to a member 
             * function getRole() on a non-object
             */
            $roles[$k] = $role instanceof RoleInterface ? $role->getRole() : (string) $role; 
        }
        
        return array_flip(array_flip($roles));
    }
    
    public function addRole($role)
    {
        ! ($role instanceof Role) && $role = new Role($role);
        
        $role->addUser($this, false);
        $this->roles->add($role);
        return $this;
    }
        
    public function removeRole($role)
    {
        $role = $this->roles->filter(
                    function(Role $r) use ($role) {
                        if ($role instanceof Role) {
                            return $r->getRole() === $role->getRole();
                        } else {
                            return $r->getRole() === strtoupper($role);
                        }
                    }
                )->first();
        if ($role) {
            $this->roles->removeElement($role);
        }    

        return $this;
    }
    
    /**
     * Nombre y apellido
     */
    public function __toString()
    {
        return sprintf('%s, %s', $this->nombre, $this->apellido);
    }
}
