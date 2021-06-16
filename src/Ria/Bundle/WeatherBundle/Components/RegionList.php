<?php

declare(strict_types=1);

namespace Ria\Bundle\WeatherBundle\Components;

use JetBrains\PhpStorm\Pure;

final class RegionList
{
    public function __construct(
        private array $regions
    ){}

    public function all(): array
    {
        return $this->regions;
    }

    #[Pure]
    public function getOnlyNames(): array
    {
        return array_keys($this->all());
    }
}