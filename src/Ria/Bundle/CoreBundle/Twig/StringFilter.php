<?php
declare(strict_types=1);

namespace Ria\Bundle\CoreBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class StringFilter extends AbstractExtension
{

    public function getFilters(): array
    {
        return [
            new TwigFilter('addcslashes', [$this, 'addcslashes'])
        ];
    }

    public function addcslashes(string $input, string $characters): string
    {
        return addcslashes($input, $characters);
    }

}