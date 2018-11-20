<?php

namespace ZipkinDoctrine\Integrations\Symfony\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('zipkin_doctrine');

        $rootNode
            ->children()
                ->arrayNode('options')
                    ->children()
                        ->scalarNode('affected_rows')
                            ->defaultValue(false)
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
