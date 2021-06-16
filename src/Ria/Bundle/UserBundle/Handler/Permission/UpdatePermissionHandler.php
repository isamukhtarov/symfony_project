<?php

declare(strict_types=1);

namespace Ria\Bundle\UserBundle\Handler\Permission;

use Ria\Bundle\UserBundle\Repository\PermissionRepository;
use Ria\Bundle\UserBundle\Command\Permission\UpdatePermissionCommand;

class UpdatePermissionHandler
{
    public function __construct(
        private PermissionRepository $permissionRepository,
    ){}

    public function handle(UpdatePermissionCommand $command)
    {
        $this->permissionRepository->save(
            $command->getPermission()
                ->setName($command->name)
        );
    }
}