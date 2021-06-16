<?php

namespace Ria\Bundle\ThemeBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class RiaThemeExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter($this->getAlias() . '.themes', $config['themes']);
        $container->setParameter($this->getAlias() . '.theme_detector', $config['theme_detector']);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $cookieOptions = null;
        if (!empty($config['cookie']['name'])) {
            $cookieOptions = [];
            foreach (['name', 'lifetime', 'path', 'domain', 'secure', 'http_only'] as $key) {
                $cookieOptions[$key] = $config['cookie'][$key];
            }
        }
        $container->setParameter($this->getAlias().'.cookie', $cookieOptions);

        $loader->load('services.yml');
    }

}