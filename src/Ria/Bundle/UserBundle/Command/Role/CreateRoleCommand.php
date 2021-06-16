<?php

declare(strict_types=1);

namespace Ria\Bundle\UserBundle\Command\Role;

use JetBrains\PhpStorm\Pure;
use Ria\Bundle\UserBundle\Entity\Role;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Ria\Bundle\CoreBundle\Validation\Constraint\UniqueColumn;

class CreateRoleCommand
{
    #[Assert\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[UniqueColumn(['entity' => Role::class, 'column' => 'name'])]
    public string $name;

    #[Assert\Valid]
    private array $permissions;

    #[Pure] public function __construct(array $permissions)
    {
        $this->permissions = $permissions;
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
}