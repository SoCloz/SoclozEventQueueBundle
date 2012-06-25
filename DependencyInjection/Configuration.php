<?php

namespace Socloz\EventQueueBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('socloz_event_queue');

        $rootNode
            ->children()
                ->scalarNode('queue_type')->end()
                ->variableNode('forward')->end()
                ->variableNode('serialize')->end()
                ->arrayNode('beanstalkd')->children()
                    ->scalarNode('host')->end()
                    ->scalarNode('port')->end()
                    ->booleanNode('persistent')->end()
                    ->scalarNode('timeout')->end()
                    ->scalarNode('tube')->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}

