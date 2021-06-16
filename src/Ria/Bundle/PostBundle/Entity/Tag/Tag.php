<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Entity\Tag;

use JetBrains\PhpStorm\Pure;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Ria\Bundle\PostBundle\Entity\Post\Post;
use Doctrine\Common\Collections\{Collection, ArrayCollection};

/**
 *
 * @ORM\Table(name="tags")
 * @ORM\Entity()
 */
class Tag
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", length=10, options={"unsigned": true})
     */
    private int $id;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    private string $slug;

    /**
     * @ManyToMany(targetEntity="Ria\Bundle\PostBundle\Entity\Post\Post", mappedBy="tags", cascade={"persist", "remove"})
     * @JoinTable(name="post_tag")
     */
    private Collection $posts;

    /**
     * @ORM\OneToMany(
     *     targetEntity="Ria\Bundle\PostBundle\Entity\Tag\Translation",
     *     mappedBy="tag",
     *     cascade={"persist", "remove"},
     *     indexBy="language"
     * )
     */
    private Collection $translations;

    #[Pure] public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->translations = new ArrayCollection();
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post))
            $this->posts->add($post);

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->contains($post))
            $this->posts->removeElement($post);

        return $this;
    }

    public function getId(): int
    {
        return $this->id;
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
            $this->translations->add($translation);
            $translation->setTag($this);
        }

        return $this;
    }
}