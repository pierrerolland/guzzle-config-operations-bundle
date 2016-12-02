<?php

namespace Guzzle\ConfigOperationsBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler pass that will add our definitions to Guzzle clients.
 *
 * @author Pierre Rolland <roll.pierre@gmail.com>
 */
class ClientCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $taggedServices = $container->findTaggedServiceIds(
            'guzzle.client'
        );

        $definitions = array();
        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                $definition = new Definition();
                $definition->setClass('Guzzle\ConfigOperationsBundle\GuzzleClient');
                $definition->setFactory(array(
                    new Reference('guzzle_config_operations.factory'),
                    'getClient'
                ));
                $definition->addArgument($id);
                $definitions['guzzle_client.' . $attributes['alias']] = $definition;
            }
        }

        $container->addDefinitions($definitions);
    }
}
