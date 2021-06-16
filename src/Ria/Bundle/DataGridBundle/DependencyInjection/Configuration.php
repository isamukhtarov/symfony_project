<?php
declare(strict_types=1);

namespace Ria\Bundle\DataGridBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('data_grid');
        $rootNode = $treeBuilder->getRootNode();

        $this->addGridConfiguration($rootNode);
        $this->addPaginatorConfiguration($rootNode);

        return $treeBuilder;
    }

    private function addGridConfiguration(ArrayNodeDefinition $node): void
    {
        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('grid')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('hydrator_class')
                            ->defaultValue('Ria\Bundle\DataGridBundle\Hydrators\DataGridHydrator')
                        ->end()
                        ->scalarNode('default_twig')
                            ->cannotBeEmpty()
                            ->defaultValue('@DataGrid/Grid/bootstrap-4-grid.html.twig')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function addPaginatorConfiguration(ArrayNodeDefinition $node): void
    {
        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('paginator')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('default_twig')
                            ->cannotBeEmpty()
                            ->defaultValue('@DataGrid/Paginator/bootstrap-4-paginator.html.twig')
                        ->end()
                        ->scalarNode('item_count_in_page')
                            ->cannotBeEmpty()
                            ->defaultValue(15)
                        ->end()
                        ->scalarNode('visible_page_count_in_paginator')
                            ->cannotBeEmpty()
                            ->defaultValue(5)
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

}
