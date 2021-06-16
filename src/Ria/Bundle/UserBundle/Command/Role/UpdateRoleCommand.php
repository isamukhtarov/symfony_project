<?php

declare(strict_types=1);

namespace Ria\Bundle\UserBundle\Command\Role;

use JetBrains\PhpStorm\Pure;
use Ria\Bundle\UserBundle\Entity\Role;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateRoleCommand
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Length(max: 255)]
    public string $name;

    #[Assert\Valid]
    private array $permissions;

    private Role $role;

    #[Pure] public function __construct(Role $role, array $permissions)
    {
        $this->name = $role->getName();
        $this->permissions = $permissions;
        $this->role = $role;
    }

    public function setPermissions(array $permissions): self
    {
        $this->permissions = $permissions;

        return $this;
    }

    #[Pure] public function getPermissions(bool $toCollection = false): array|Collection
    {
        return (!$toCollection) ? $this->permissions : new ArrayCollection($this->permissions);
    }

    public function getRole(): Role
    {
        return $this->role;
    }
}