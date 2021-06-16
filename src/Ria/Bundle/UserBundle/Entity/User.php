<?php

declare(strict_types=1);

namespace Ria\Bundle\UserBundle\Entity;

use DateTime;
use JetBrains\PhpStorm\Pure;
use Doctrine\ORM\Mapping as ORM;
use Ria\Bundle\CoreBundle\Entity\Status;
use Ria\Bundle\PhotoBundle\Entity\Photo;
use Ria\Bundle\UserBundle\Security\HasRoles;
use Ria\Bundle\CoreBundle\Entity\HasRelations;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\{Collection, ArrayCollection};
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="Ria\Bundle\UserBundle\Repository\UserRepository")
 * @UniqueEntity("email")
 */
class User implements UserInterface
{
    use HasRoles, HasRelations;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     */
    private string $email;

    /**
     * @ORM\Column(type="string", name="email_additional", length=50, nullable=true)
     */
    private ?string $emailAdditional = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $password;

    /**
     * @ORM\Embedded(class="Ria\Bundle\CoreBundle\Entity\Status", columnPrefix=false)
     */
    private Status $status;

    /**
     * @ORM\Column(type="string", nullable=false, columnDefinition="ENUM('male', 'female')")
     */
    private string $gender = '';

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private ?string $phone = null;

    /**
     * @ORM\Column(type="date", name="birthdate")
     */
    private DateTime $birthDate;

    /**
     * @ORM\ManyToOne(targetEntity="Ria\Bundle\PhotoBundle\Entity\Photo")
     * @ORM\JoinColumn(name="photo_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private ?Photo $photo = null;

    /**
     * @ORM\Column(type="datetime", name="created_at", columnDefinition="TIMESTAMP DEFAULT CURRENT_TIMESTAMP")
     */
    private DateTime $createdAt;

    /**
     * @ORM\ManyToMany(targetEntity="Ria\Bundle\UserBundle\Entity\Role")
     */
    private Collection $roles;

    /**
     * @ORM\ManyToMany(targetEntity="Ria\Bundle\UserBundle\Entity\Permission", indexBy="name")
     */
    private Collection $permissions;

    /**
     * @ORM\OneToMany(targetEntity="Ria\Bundle\PostBundle\Entity\Post\Post", mappedBy="author", cascade={"persist", "remove"})
     */
    private Collection $posts;

    /**
     * @ORM\OneToMany(
     *     targetEntity="Ria\Bundle\UserBundle\Entity\Translation",
     *     mappedBy="user",
     *     cascade={"persist", "remove"},
     *     indexBy="language",
     *     fetch="EAGER"
     *  )
     */
    private Collection $translations;

    #[Pure] public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->permissions = new ArrayCollection();
        $this->translations = new ArrayCollection();
        $this->posts = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getEmailAdditional(): ?string
    {
        return $this->emailAdditional;
    }

    public function setEmailAdditional(?string $emailAdditional): self
    {
        $this->emailAdditional = $emailAdditional;

        return $this;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function setStatus(Status $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getPhoto(): ?Photo
    {
        return $this->photo;
    }

    public function setPhoto(?Photo $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->getPhoto()?->getFilename();
    }

    public function getGender(): string
    {
        return $this->gender;
    }

    public function setGender(string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getBirthDate(): DateTime
    {
        return $this->birthDate;
    }

    public function setBirthDate(DateTime $birthDate): self
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    public function getRolesRelation(): Collection
    {
        return $this->roles;
    }

    public function getFirstRole(): ?Role
    {
        return $this->roles->first() ?: null;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER']; // Default role for any user.
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function getTranslation(string $language): ?Translation
    {
        return $this->translations->get($language);
    }

    public function addTranslation(Translation $translation): self
    {
        if (!$this->translations->contains($translation)) {
            $this->translations[$translation->getLanguage()] = $translation;
            $translation->setUser($this);
        }
        return $this;
    }

    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function getUsername(): string
    {
        return $this->email;
    }

    public function eraseCredentials()
    {
        //
    }

    public function __toString(): string
    {
        return (string) $this->getId();
    }
}
