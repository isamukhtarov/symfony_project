<?php

namespace Ria\Bundle\PostBundle\Entity\Story;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Ria\Bundle\CoreBundle\Entity\Meta;

/**
 * @ORM\Table(name="stories_lang", uniqueConstraints={@UniqueConstraint(name="slug", columns={"slug", "language"})})
 * @ORM\Entity
 */
class Translation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string")
     */
    private string $slug;

    /**
     * @ORM\Column(type="string")
     */
    private string $title;

    /**
     * @ORM\Column(type="text")
     */
    private string $description;

    /**
     * @ORM\Column(type="string")
     */
    public string $language;

    /**
     * @ORM\Embedded(class="Ria\Bundle\CoreBundle\Entity\Meta", columnPrefix=false)
     * @var Meta
     */
    private Meta $meta;

    /**
     * @ORM\ManyToOne(targetEntity="Ria\Bundle\PostBundle\Entity\Story\Story", inversedBy="translations")
     * @JoinColumn(name="story_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private Story $story;

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function setLanguage(string $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
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

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getStory(): Story
    {
        return $this->story;
    }

    public function setStory(Story $story): self
    {
        $this->story = $story;

        return $this;
    }

    public function getMeta(): Meta
    {
        return $this->meta;
    }

    public function setMeta(Meta $meta): self
    {
        $this->meta = $meta;

        return $this;
    }
}