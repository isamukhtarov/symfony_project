<?php

declare(strict_types=1);

namespace Ria\Bundle\UserBundle\Handler\Role;

use Ria\Bundle\UserBundle\Command\Role\CreateRoleCommand;
use Ria\Bundle\UserBundle\Entity\Role;
use Ria\Bundle\UserBundle\Repository\RoleRepository;

class CreateRoleHandler
{
    public function __construct(
        private RoleRepository $roleRepository,
    ){}

    public function handle(CreateRoleCommand $command): void
    {
        $role = new Role();
        $role->setName($command->name)
            ->sync('permissions', $command->getPermissions(true));
        $this->roleRepository->save($role);
    }
}