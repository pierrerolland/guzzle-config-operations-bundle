<?php

namespace Guzzle\ConfigOperationsBundle\DependencyInjection\CompilerPass;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Serializer\Serializer;

class SymfonyCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('serializer')
            && $container->getDefinition('serializer')->getClass() === Serializer::class) {
            $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../../Resources/config'));
            $loader->load('symfony_normalizer.yml');
        }
    }
}
