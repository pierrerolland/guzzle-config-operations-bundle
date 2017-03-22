<?php

namespace Guzzle\ConfigOperationsBundle;

use Guzzle\ConfigOperationsBundle\DependencyInjection\CompilerPass\ClientCompilerPass;
use Guzzle\ConfigOperationsBundle\DependencyInjection\CompilerPass\SymfonyCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class GuzzleConfigOperationsBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ClientCompilerPass());
        $container->addCompilerPass(new SymfonyCompilerPass());
    }
}
