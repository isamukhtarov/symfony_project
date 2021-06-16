<?php

declare(strict_types=1);

namespace Ria\Bundle\UserBundle\Command\Permission;

use JetBrains\PhpStorm\Pure;
use Ria\Bundle\UserBundle\Entity\Permission;
use Symfony\Component\Validator\Constraints as Assert;

class UpdatePermissionCommand
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Length(max: 255)]
    public string $name;

    private Permission $permission;

    #[Pure] public function __construct(Permission $permission)
    {
        $this->name = $permission->getName();
        $this->permission = $permission;
    }

    public function getPermission(): Permission
    {
        return $this->permission;
    }
}