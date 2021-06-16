<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Command\Region;

class DeleteRegionCommand
{
    public function __construct(
        private int $id,
    ){}

    public function getId(): int
    {
        return $this->id;
    }
}