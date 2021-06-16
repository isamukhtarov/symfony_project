<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Form\DataTransformer;

use JetBrains\PhpStorm\Pure;
use Ria\Bundle\PostBundle\Entity\Post\Export;

class ExportDataTransformer
{
    #[Pure] public function transform(): array
    {
        $exports = [];
        foreach (Export::all() as $export)
            $exports[ucfirst($export)] = $export;
        return $exports;
    }
}