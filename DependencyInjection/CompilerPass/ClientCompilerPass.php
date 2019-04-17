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

        $definitions = [];
        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                $definitionId = sprintf('guzzle_client.%s', $attributes['alias']);
                if (!$container->has($definitionId)) {
                    $definition = new Definition();
                    $definition->setClass('Guzzle\ConfigOperationsBundle\GuzzleClient');
                    $definition->setFactory([
                        new Reference('guzzle_config_operations.factory'),
                        'getClient'
                    ]);
                    $definition->setPublic(true);
                    $definition->addArgument($id);
                    $definitions[$definitionId] = $definition;
                }
            }
        }

        $container->addDefinitions($definitions);
    }
}
