<?php

namespace Ria\Bundle\ThemeBundle\DependencyInjection\Compiler;

use Ria\Bundle\ThemeBundle\Twig\FilesystemLoader;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ThemePass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $container->findDefinition('twig.loader.filesystem')
            ->setClass(FilesystemLoader::class)
            ->addMethodCall('setActiveTheme', [new Reference('ria_theme.active_theme')]);
    }
}