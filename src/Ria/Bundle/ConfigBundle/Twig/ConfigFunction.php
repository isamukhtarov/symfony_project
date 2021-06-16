<?php

declare(strict_types=1);

namespace Ria\Bundle\ConfigBundle\Twig;

use Ria\Bundle\ConfigBundle\Service\ConfigPackage;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ConfigFunction extends AbstractExtension
{

    protected ConfigPackage $configPackage;

    public function __construct(ConfigPackage $configPackage)
    {
        $this->configPackage = $configPackage;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_config', function(string $name) {
                return $this->configPackage->get($name);
            }, ['is_safe' => ['all']])
        ];
    }
}