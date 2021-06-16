<?php

namespace Ria\Bundle\ThemeBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('ria_theme');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('theme_detector')->cannotBeEmpty()->end()
                ->arrayNode('themes')
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('cookie')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('name')->cannotBeEmpty()->end()
                        ->scalarNode('lifetime')->defaultValue(31536000)->end()
                        ->scalarNode('path')->defaultValue('/')->end()
                        ->scalarNode('domain')->defaultValue('')->end()
                        ->booleanNode('secure')->defaultFalse()->end()
                        ->booleanNode('http_only')->defaultFalse()->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}