<?php

declare(strict_types=1);

namespace Ria\Bundle\AdminBundle\Voter;

use Ria\Bundle\UserBundle\Entity\User;
use Ria\Bundle\UserBundle\Entity\Permission;
use Ria\Bundle\UserBundle\Repository\PermissionRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

abstract class RiaAbstractVoter extends Voter
{
    public function __construct(
        private PermissionRepository $permissionRepository
    ){}

    protected function supports(string $attribute, $subject): bool
    {
        return true; // Supports always.
    }

    public function can(UserInterface|User $user, string $accessIdentifier): bool
    {
        if ($user->hasRole($accessIdentifier))
            return true;

        /** @var Permission $permission */
        if ($permission = $this->permissionRepository->findOneBy(['name' => $accessIdentifier]))
            if ($user->hasPermissionTo($permission))
                return true;

        return false;
    }
}