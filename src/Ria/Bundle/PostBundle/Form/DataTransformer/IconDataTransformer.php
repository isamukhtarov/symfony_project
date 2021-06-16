<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Form\DataTransformer;

use JetBrains\PhpStorm\Pure;
use Ria\Bundle\PostBundle\Entity\Post\Icon;
use Symfony\Contracts\Translation\TranslatorInterface;

class IconDataTransformer
{
    public function __construct(
        private TranslatorInterface $translator,
    ){}

    #[Pure] public function transform(): array
    {
        $data = [];
        foreach (Icon::all() as $status)
            $data[ucfirst($status)] = $status;
        return $data;
    }
}