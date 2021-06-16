<?php

declare(strict_types=1);

namespace Ria\Bundle\UserBundle\Handler\Role;

use Ria\Bundle\UserBundle\Repository\RoleRepository;
use Ria\Bundle\UserBundle\Command\Role\DeleteRoleCommand;

class DeleteRoleHandler
{
    public function __construct(
        private RoleRepository $roleRepository,
    ){}

    public function handle(DeleteRoleCommand $command): void
    {
        if (($role = $this->roleRepository->find($command->getId())) === null) return;
        $this->roleRepository->remove($role);
    }
}