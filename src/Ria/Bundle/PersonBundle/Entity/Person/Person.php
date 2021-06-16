<?php
declare(strict_types=1);

namespace Ria\Bundle\PersonBundle\Entity\Person;

use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Ria\Bundle\CoreBundle\Entity\HasRelations;
use Ria\Bundle\PhotoBundle\Entity\Photo;
use Ria\Bundle\PostBundle\Entity\Post\Post;
use Doctrine\Common\Collections\{Collection, ArrayCollection};

/**
 * @ORM\Table(name="persons")
 * @ORM\Entity(repositoryClass="Ria\Bundle\PersonBundle\Query\Repositories\PersonRepository")
 */
class Person
{
    use HasRelations;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", length=11, options={"unsigned": true})
     */
    private int $id;

    /**
     * @ORM\Column (type="boolean")
     */
    private bool $status;

    /**
     * @ORM\Embedded(class="Ria\Bundle\PersonBundle\Entity\Person\Type", columnPrefix=false)
     * @var Type
     */
    private Type $type;

    /**
     * @ORM\ManyToOne(targetEntity="Ria\Bundle\PhotoBundle\Entity\Photo")
     * @ORM\JoinColumn(name="photo_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private Photo|null $photo;

    /**
     * @ORM\OneToMany(targetEntity="Ria\Bundle\PersonBundle\Entity\Person\PersonPhoto", mappedBy="person", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"sort" = "ASC"})
     */
    private Collection $photoRelations;

    /**
     * @ORM\OneToMany(
     *     targetEntity="\Ria\Bundle\PersonBundle\Entity\Person\Translation",
     *     mappedBy="person",
     *     cascade={"persist", "remove"},
     *     indexBy="language"
     *     )
     */
    private Collection $translations;

    /**
     * @ORM\ManyToMany(targetEntity="Ria\Bundle\PostBundle\Entity\Post\Post", mappedBy="persons", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="post_person")
     */
    private Collection $posts;

    #[Pure] public function __construct()
    {
        $this->translations = new ArrayCollection();
        $this->photoRelations = new ArrayCollection();
        $this->posts        = new ArrayCollection();
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function setType(Type $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }


    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function setPhoto($photo): self
    {
        $this->photo = $photo;
        return $this;
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
            $translation->setPerson($this);
        }

        return $this;
    }

    public function getStatus(): bool
    {
        return $this->status;
    }

    public function getPhoto(): ?Photo
    {
        return $this->photo;
    }

    public function getPhotos(): Collection
    {
        return $this->photoRelations->map(fn (PersonPhoto $personPhoto) =>  $personPhoto->getPhoto());
    }

    public function getPhotoRelation(): Collection
    {
        return $this->photoRelations;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->contains($post)) {
            $this->posts->removeElement($post);
        }

        return $this;
    }
}