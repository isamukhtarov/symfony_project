<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Form\DataTransformer;

use JetBrains\PhpStorm\Pure;
use Ria\Bundle\PostBundle\Entity\Post\Status;

class StatusDataTransformer
{
    #[Pure] public function transform(): array
    {
        $data = [];
        foreach (Status::list() as $status)
            $data[$status] = $status;
        return $data;
    }
}