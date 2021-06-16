<?php

declare(strict_types=1);

namespace Ria\Bundle\UserBundle\Command\User;

use DateTime;
use JetBrains\PhpStorm\Pure;
use Ria\Bundle\PhotoBundle\Entity\Photo;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

abstract class AbstractUserCommand
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Length(max: 50)]
    #[Assert\Email]
    public string $email;

    #[Assert\Type('string')]
    #[Assert\Length(max: 50)]
    #[Assert\Email]
    public ?string $emailAdditional = null;

    #[Assert\Type('string')]
    #[Assert\NotBlank]
    public string $gender;

    #[Assert\Type('string')]
    #[Assert\Length(max: 100)]
    public ?string $phone = null;

    #[Assert\Type('datetime')]
    #[Assert\NotBlank]
    public DateTime $birthDate;

    #[Assert\Type('integer')]
    public int $status = 0;

    #[Assert\Valid]
    public array $translations;

    public Photo|int|null $photo = null;

    public function __construct(
        private array $roles,
        private array $permissions,
    ){}

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
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

    #[Pure] public function getRoles(bool $toCollection = false): array|Collection
    {
        return (!$toCollection) ? $this->roles : new ArrayCollection($this->roles);
    }
}