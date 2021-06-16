<?php

declare(strict_types=1);

namespace Ria\Bundle\UserBundle\Handler\Role;

use Ria\Bundle\UserBundle\Repository\RoleRepository;
use Ria\Bundle\UserBundle\Command\Role\UpdateRoleCommand;

class UpdateRoleHandler
{
    public function __construct(
        private RoleRepository $roleRepository,
    ){}

    public function handle(UpdateRoleCommand $command)
    {
        $this->roleRepository->save(
            $command->getRole()
                ->setName($command->name)
                ->sync('permissions', $command->getPermissions(true))
        );
    }
}