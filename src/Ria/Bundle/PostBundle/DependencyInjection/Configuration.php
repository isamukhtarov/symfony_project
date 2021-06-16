<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        return new TreeBuilder('ria_post');
    }
}