<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Entity\Category;

use DateTime;
use JetBrains\PhpStorm\Pure;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\{Collection, ArrayCollection};

/**
 * @ORM\Table(name="categories")
 * @ORM\Entity(repositoryClass="Ria\Bundle\PostBundle\Query\Repository\CategoryRepository")
 */
class Category
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\OneToMany(targetEntity="Ria\Bundle\PostBundle\Entity\Category\Category", mappedBy="parent")
     */
    private Collection $children;

    /**
     * @ORM\ManyToOne(targetEntity="Ria\Bundle\PostBundle\Entity\Category\Category", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true)
     */
    private ?Category $parent = null;

    /**
     * @ORM\Embedded(class="Ria\Bundle\PostBundle\Entity\Category\Template", columnPrefix=false)
     */
    private Template $template;

    /**
     * @ORM\Column(name="sort", type="integer")
     */
    private int $sort = 0;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $status;

    /**
     * @ORM\Column(type="datetime", name="created_at", columnDefinition="TIMESTAMP DEFAULT CURRENT_TIMESTAMP")
     */
    private DateTime $createdAt;

    /**
     * @ORM\OneToMany(
     *     targetEntity="Ria\Bundle\PostBundle\Entity\Category\Translation",
     *     mappedBy="category",
     *     cascade={"persist", "remove"},
     *     indexBy="language"
     *     )
     */
    private Collection $translations;

    /**
     * @ORM\OneToMany(
     *     targetEntity="Ria\Bundle\PostBundle\Entity\Post\Post",
     *     mappedBy="category",
     *     cascade={"persist", "remove"}
     *  )
     */
    private Collection $posts;

    #[Pure] public function __construct()
    {
        $this->translations = new ArrayCollection();
        $this->children     = new ArrayCollection();
        $this->posts        = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTemplate(): Template
    {
        return $this->template;
    }

    public function getStatus(): bool
    {
        return $this->status;
    }

    public function getTranslation(string $language): ?Translation
    {
        return $this->translations->get($language);
    }

    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function addTranslation(Translation $translation): self
    {
        if (!$this->getTranslations()->contains($translation)) {
            $this->translations->add($translation);
            $translation->setCategory($this);
        }

        return $this;
    }

    public function setTemplate(Template $template): self
    {
        $this->template = $template;

        return $this;
    }

    public function getSort(): int
    {
        return $this->sort;
    }

    public function setSort(int $sort): self
    {
        $this->sort = $sort;

        return $this;
    }

    public function incrementSort(): self
    {
        $this->sort++;

        return $this;
    }

    public function decrementSort(int $sort): self
    {
        $this->sort = $sort;

        return $this;
    }

    public function isParent(): bool
    {
        return $this->parent === null;
    }

    public function setParent(?Category $parent = null): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function getParent(): ?Category
    {
        return $this->parent;
    }

    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getPosts(): Collection
    {
        return $this->posts;
    }
}