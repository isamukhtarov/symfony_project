<?php

declare(strict_types=1);

namespace Ria\Bundle\UserBundle\Command\Permission;

class DeletePermissionCommand
{
    public function __construct(
        private int $id,
    ){}

    public function getId(): int
    {
        return $this->id;
    }
}
