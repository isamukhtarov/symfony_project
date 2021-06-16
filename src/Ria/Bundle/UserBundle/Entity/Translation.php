<?php

declare(strict_types=1);

namespace Ria\Bundle\UserBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Ria\Bundle\CoreBundle\Entity\Meta;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(name="users_lang", uniqueConstraints={@UniqueConstraint(name="slug", columns={"slug", "language"})})
 */
class Translation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", name="first_name", length=100)
     */
    private string $firstName;

    /**
     * @ORM\Column(type="string", name="last_name", length=100)
     */
    private string $lastName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $position;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private string $slug;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $description;

    /**
     * @ORM\Column(type="string", length=2, options={"fixed": true})
     */
    public string $language;

    /**
     * @ORM\Embedded(class="Ria\Bundle\CoreBundle\Entity\Meta", columnPrefix=false)
     * @var Meta
     */
    private Meta $meta;

    /**
     * @ORM\Column(type="datetime", columnDefinition="TIMESTAMP DEFAULT CURRENT_TIMESTAMP")
     */
    private DateTime $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="Ria\Bundle\UserBundle\Entity\User", inversedBy="translations")
     * @JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="cascade")
     */
    private UserInterface $user;

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function setLanguage(string $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function setPosition(?string $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function setUser(UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function setMeta(Meta $meta): self
    {
        $this->meta = $meta;

        return $this;
    }

    public function getMeta(): Meta
    {
        return $this->meta;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getFullName(): string
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }
}
