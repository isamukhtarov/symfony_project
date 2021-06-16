<?php

declare(strict_types=1);

namespace Ria\Bundle\UserBundle\Handler\Permission;

use Ria\Bundle\UserBundle\Entity\Permission;
use Ria\Bundle\UserBundle\Repository\PermissionRepository;
use Ria\Bundle\UserBundle\Command\Permission\CreatePermissionCommand;

class CreatePermissionHandler
{
    public function __construct(
        private PermissionRepository $permissionRepository,
    ){}

    public function handle(CreatePermissionCommand $command)
    {
        $this->permissionRepository->save((new Permission())->setName($command->name));
    }
}