<?php

declare(strict_types=1);

namespace Ria\Bundle\VoteBundle\Form\DataTransformer;

use JetBrains\PhpStorm\Pure;
use Ria\Bundle\VoteBundle\Entity\Type;

class TypeDataTransformer
{
    #[Pure]public function transform(): array
    {
        $data = [];
        foreach (Type::all() as $type) {
            $data[$type] = $type;
        }
        return $data;
    }
}