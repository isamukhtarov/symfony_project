<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Entity\Story;

use DateTime;
use JetBrains\PhpStorm\Pure;
use Doctrine\ORM\Mapping as ORM;
use Ria\Bundle\PhotoBundle\Entity\Photo;
use Doctrine\Common\Collections\{Collection, ArrayCollection};

/**
 * @ORM\Entity(repositoryClass="Ria\Bundle\PostBundle\Query\Repository\StoryRepository")
 * @ORM\Table(name="stories")
 */
class Story
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity="Ria\Bundle\PhotoBundle\Entity\Photo")
     * @ORM\JoinColumn(name="cover", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private ?Photo $cover = null;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $status;

    /**
     * @ORM\Column(type="boolean", name="show_on_site")
     */
    private bool $showOnSite;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     */
    private DateTime $createdAt;

    /**
     * @ORM\OneToMany(
     *     targetEntity="Ria\Bundle\PostBundle\Entity\Story\Translation",
     *     mappedBy="story",
     *     cascade={"persist", "remove"},
     *     indexBy="language"
     *     )
     */
    private Collection $translations;

    /**
     * @ORM\OneToMany(targetEntity="Ria\Bundle\PostBundle\Entity\Post\Post", mappedBy="story", cascade={"persist", "remove"})
     */
    private Collection $posts;

    #[Pure] public function __construct()
    {
        $this->translations = new ArrayCollection();
        $this->posts        = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getStatus(): bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function isShownOnSite(): bool
    {
        return $this->showOnSite;
    }

    public function setShowOnSite(bool $canShowOnSite): self
    {
        $this->showOnSite = $canShowOnSite;
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
        if (!$this->getTranslations()->contains($translation)) {
            $this->translations->add($translation);
            $translation->setStory($this);
        }

        return $this;
    }

    public function getCover(): ?Photo
    {
        return $this->cover;
    }

    public function setCover(?Photo $photo): self
    {
        $this->cover = $photo;
        return $this;
    }

    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}