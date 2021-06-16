<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Command\Category;

class MoveUpCategoryCommand
{
    public function __construct(
        private int $id
    ){}

    public function getId(): int
    {
        return $this->id;
    }
}