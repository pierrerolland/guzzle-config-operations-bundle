<?php

namespace Guzzle\ConfigOperationsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('guzzle_config_operations');

        $rootNode
            ->children()
                ->arrayNode('clients')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('baseUrl')->end()
                            ->scalarNode('apiVersion')->end()
                            ->scalarNode('description')->end()
                            ->arrayNode('includes')
                                ->prototype('scalar')->end()
                            ->end()
                            ->arrayNode('operations')
                                ->useAttributeAsKey('operationName')
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('extends')->end()
                                        ->enumNode('httpMethod')
                                            ->values(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'])
                                        ->end()
                                        ->scalarNode('uri')->end()
                                        ->scalarNode('summary')->end()
                                        ->scalarNode('class')->end()
                                        ->scalarNode('responseClass')->end()
                                        ->scalarNode('responseNotes')->end()
                                        ->enumNode('responseType')
                                            ->values(['primitive', 'class', 'model', 'documentation'])
                                        ->end()
                                        ->booleanNode('deprecated')->end()
                                        ->arrayNode('errorResponses')
                                            ->prototype('array')
                                                ->children()
                                                    ->scalarNode('code')->end()
                                                    ->scalarNode('reason')->end()
                                                    ->scalarNode('class')->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('data')->end()
                                        ->arrayNode('parameters')
                                            ->useAttributeAsKey('name')
                                            ->prototype('array')
                                                ->children()
                                                    ->variableNode('type')->end()
                                                    ->scalarNode('instanceOf')->end()
                                                    ->booleanNode('required')->end()
                                                    ->variableNode('default')->end()
                                                    ->booleanNode('static')->end()
                                                    ->scalarNode('description')->end()
                                                    ->scalarNode('location')->end()
                                                    ->scalarNode('sentAs')->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->variableNode('models')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
