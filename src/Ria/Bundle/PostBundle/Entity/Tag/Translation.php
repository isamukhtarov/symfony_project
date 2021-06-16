<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Entity\Tag;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * @ORM\Table(name="tags_lang", uniqueConstraints={@UniqueConstraint(name="name_unique", columns={"name", "language"})})
 * @ORM\Entity()
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
     * @ORM\Column(type="string")
     */
    private string $name;

    /**
     * @ORM\Column(type="string")
     */
    private string $language;

    /**
     * @ORM\Column(type="integer")
     */
    private int $count = 0;

    /**
     * @ORM\ManyToOne(targetEntity="Ria\Bundle\PostBundle\Entity\Tag\Tag", inversedBy="translations")
     * @JoinColumn(name="tag_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private Tag $tag;

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function setLanguage(string $language): self
    {
        $this->language = $language;

        return $this;
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

    public function getTag(): Tag
    {
        return $this->tag;
    }

    public function setTag(Tag $tag): self
    {
        $this->tag = $tag;

        return $this;
    }

    public function incrementCount(): self
    {
        $this->count++;

        return $this;
    }

    public function decrementCount(): self
    {
        $this->count--;

        return $this;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}
