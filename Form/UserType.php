<?php

namespace DocDigital\Bundle\UserBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use\Doctrine\ORM\EntityRepository;

/**
 * 
 * @author Juan Manuel Fernandez <juanmf@gmail.com>
 */
class UserType extends BaseType
{
    /**
     * 
     * @var EntityManager
     */
    private $em = null;

    /**
     * Constructor
     *
     * @param string $class The User class name
     * @param array $roleHierarchy Role Hierarchy
     */
    public function __construct($class, $em)
    {
        parent::__construct($class);
        
        $this->em = $em;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add(
                'enabled',
                null,
                array(
                    'label'    => 'Habilitado',
                    'required' => false,
                )
            )
            ->add('nombre')
            ->add('apellido')
            ->add(
                'rolesCollection',
                'entity',
                array(
                    'class' => 'DdUserBundle:Role',
//                    'choices'  => $this->getRoleChoices(),
                    /*'choices'  => $this->roleHierarchy,*/
//                    'query_builder'  => function(EntityRepository $repo) {
//                            $repo->createQueryBuilder('r');
//                        },
                    'multiple' => true,
                    'expanded' => true,
                    'attr'     => array('class' => 'single-line-checks')
                )
            )
            //->add('enabledLocations', null, array('multiple' => true))
            ;
            
//        $builder->addEventSubscriber(new UserRolesEventSubscriber());

    }

    public function getName()
    {
        return 'application_userbundle_user';
    }

    protected function getRoleChoices()
    {
        $roots = $this->em->getRepository('DdUserBundle:Role')->findAll();
        $choices = array();

        foreach ($roots as $role)
        {
            $choices[$role->getId()] = $role;
        }

        return $choices;
    }
}
