<?php

declare(strict_types=1);

namespace Ria\Bundle\UserBundle\Handler\Permission;

use Ria\Bundle\UserBundle\Command\Permission\DeletePermissionCommand;
use Ria\Bundle\UserBundle\Repository\PermissionRepository;

class DeletePermissionHandler
{
    public function __construct(
        private PermissionRepository $permissionRepository,
    ){}

    public function handle(DeletePermissionCommand $command): void
    {
        if (($permission = $this->permissionRepository->find($command->getId())) === null) return;
        $this->permissionRepository->remove($permission);
    }
}