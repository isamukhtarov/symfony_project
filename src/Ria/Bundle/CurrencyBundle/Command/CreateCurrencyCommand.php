<?php

declare(strict_types=1);

namespace Ria\Bundle\CurrencyBundle\Command;

class CreateCurrencyCommand
{
    public function __construct(
        private array $resources
    ){}

    public function getResources(): array
    {
        return $this->resources;
    }
}