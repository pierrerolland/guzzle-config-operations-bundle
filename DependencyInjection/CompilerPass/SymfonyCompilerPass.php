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
     * @var Loader\YamlFileLoader
     */
    private $loader;

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('serializer')
            && $container->getDefinition('serializer')->getClass() === Serializer::class) {
            $this->getFileLoader($container)->load('symfony_normalizer.yml');
        }
    }

    /**
     * @param ContainerBuilder $container
     *
     * @return Loader\YamlFileLoader
     */
    public function getFileLoader(ContainerBuilder $container)
    {
        if (!$this->loader) {
            $this->loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../../Resources/config'));
        }

        return $this->loader;
    }

    /**
     * @param Loader\YamlFileLoader $loader
     */
    public function setFileLoader(Loader\YamlFileLoader $loader)
    {
        $this->loader = $loader;
    }
}
