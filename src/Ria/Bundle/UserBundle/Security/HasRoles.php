<?php

declare(strict_types=1);

namespace Ria\Bundle\UserBundle\Security;

use JetBrains\PhpStorm\Pure;
use Ria\Bundle\UserBundle\Entity\Role;

trait HasRoles
{
    use HasPermissions;

    public function hasRole($value): bool
    {
        if (is_string($value) && str_contains($value, '|'))
            $value = $this->convertPipeToArray($value);

        $roles = $this->getRolesRelation();

        if (is_string($value)) {
            foreach ($roles as $role)
                if (strcasecmp($role->getName(), $value) === 0) return true;
            return false;
        }

        if (is_array($value)) {
            foreach ($value as $role)
                if ($this->hasRole($role)) return true;
            return false;
        }

        return $roles->contains($value);
    }

    public function hasAnyRole(...$roles): bool
    {
        return $this->hasRole($roles);
    }

    public function getRoleNames(): array
    {
        return array_map(fn (Role $role) => $role->getName(), $this->getRolesRelation()->toArray());
    }

    #[Pure] private function convertPipeToArray(string $roles): array
    {
        return explode('|', $roles);
    }
}
