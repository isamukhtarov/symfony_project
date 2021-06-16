<?php

namespace Ria\Bundle\DataGridBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class DataGridExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $this->remapParameters($config, $container, array(
            'grid'  => 'data_grid.grid',
            'paginator'  => 'data_grid.paginator',
        ));

        $container->setParameter('data_grid.grid.hydrator_class', $config['grid']['hydrator_class']);
    }

    protected function remapParameters(array $config, ContainerBuilder $container, array $map): void
    {
        foreach ($map as $name => $paramName) {
            if (isset($config[$name])) {
                $container->setParameter($paramName, $config[$name]);
            }
        }
    }

}
