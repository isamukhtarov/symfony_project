<?php

declare(strict_types=1);

namespace Ria\Bundle\StatisticBundle\Twig;

use JetBrains\PhpStorm\Pure;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FilterExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('selectedFilter', [$this, 'getSelectedFilter'], ['is_safe' => ['all']])
        ];
    }

    #[Pure]public function getSelectedFilter(string $value, ?string $filter = null): string
    {
        if (($filter === $value) || (is_null($filter) && $value === 'month')) {
            return 'selected';
        }
        return '';
    }
}