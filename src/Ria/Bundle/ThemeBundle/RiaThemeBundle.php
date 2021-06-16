<?php
declare(strict_types=1);

namespace Ria\Bundle\ThemeBundle;

use Ria\Bundle\ThemeBundle\DependencyInjection\Compiler\ThemePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class RiaUserBundle
 * @package Ria\Bundle\PostBundle
 */
class RiaThemeBundle extends Bundle
{

    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ThemePass());
    }

}