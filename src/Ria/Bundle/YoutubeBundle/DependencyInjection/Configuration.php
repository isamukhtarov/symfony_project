<?php

declare(strict_types=1);

namespace Ria\Bundle\YoutubeBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): object
    {
        return new TreeBuilder('ria_youtube');
    }
}