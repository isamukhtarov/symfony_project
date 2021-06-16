<?php

declare(strict_types=1);

namespace Ria\Bundle\ConfigBundle\Command;

use JetBrains\PhpStorm\Pure;
use Ria\Bundle\ConfigBundle\Entity\Config;

class ConfigUpdateCommand
{
    public int $id;

    public string|null $value;

    public string|null $label;

    #[Pure]public function __construct(Config $config)
    {
        $this->id = $config->getId();
        $this->value = $config->getValue();
        $this->label = $config->getLabel();
    }
}