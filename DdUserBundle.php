<?php

namespace DocDigital\Bundle\UserBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;

class DdUserBundle extends Bundle
{
    
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $mappings = array(
            realpath(__DIR__ . '/Resources/config/doctrine/model') => 'FOS\UserBundle\Model',
        );

        $container->addCompilerPass(
            DoctrineOrmMappingsPass::createXmlMappingDriver(
                $mappings, array('fos_user.model_manager_name'), false
            )
        );
    }
}
