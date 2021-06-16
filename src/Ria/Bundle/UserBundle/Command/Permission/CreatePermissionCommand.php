<?php

declare(strict_types=1);

namespace Ria\Bundle\UserBundle\Command\Permission;

use Ria\Bundle\UserBundle\Entity\Permission;
use Symfony\Component\Validator\Constraints as Assert;
use Ria\Bundle\CoreBundle\Validation\Constraint\UniqueColumn;

class CreatePermissionCommand
{
    #[Assert\Type('string')]
    #[Assert\Length(max: 255)]
    #[Assert\NotBlank]
    #[UniqueColumn(['entity' => Permission::class, 'column' => 'name'])]
    public string $name;
}