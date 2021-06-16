<?php

declare(strict_types=1);

namespace Ria\Bundle\UserBundle\Entity;

use JetBrains\PhpStorm\Pure;
use Doctrine\ORM\Mapping as ORM;
use Ria\Bundle\CoreBundle\Entity\HasRelations;
use Doctrine\Common\Collections\{Collection, ArrayCollection};

/**
 * @ORM\Table(name="roles")
 * @ORM\Entity(repositoryClass="Ria\Bundle\UserBundle\Repository\RoleRepository")
 */
class Role
{
    use HasRelations;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string")
     */
    private string $name;

    /**
     * @ORM\ManyToMany(targetEntity="Ria\Bundle\UserBundle\Entity\Permission", inversedBy="roles")
     */
    private Collection $permissions;

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    #[Pure] public function __construct()
    {
        $this->permissions = new ArrayCollection();
    }

    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    public function addPermission(Permission $permission): Role
    {
        if (!$this->permissions->contains($permission))
            $this->permissions->add($permission);
        return $this;
    }
}
