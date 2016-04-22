<?php

namespace Guzzle\ConfigOperationsBundle;

use Guzzle\ConfigOperationsBundle\DependencyInjection\CompilerPass\ClientCompilerPass;
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
    }
}
