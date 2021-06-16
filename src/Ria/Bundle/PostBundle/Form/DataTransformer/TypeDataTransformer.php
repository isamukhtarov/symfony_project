<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Form\DataTransformer;

use JetBrains\PhpStorm\Pure;
use Ria\Bundle\PostBundle\Entity\Post\Type;

class TypeDataTransformer
{
    #[Pure] public function transform(): array
    {
        $data = [];
        foreach (Type::all() as $type)
            $data[$type] = $type; // translate this shit.
        return $data;
    }
}