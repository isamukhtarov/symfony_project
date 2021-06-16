<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Entity\Post\Traits;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;
use Ria\Bundle\PostBundle\Entity\Tag\Tag;
use Ria\Bundle\PostBundle\Entity\Post\Post;
use Doctrine\Common\Collections\Collection;
use Ria\Bundle\PostBundle\Entity\Tag\Translation as TagTranslation;

trait Tags
{
    /**
     * @ORM\ManyToMany(targetEntity="Ria\Bundle\PostBundle\Entity\Tag\Tag", inversedBy="posts", cascade={"persist", "remove"})
     * @JoinTable(name="post_tag")
     */
    private Collection $tags;

    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        /** @var Post $this */
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
            $tag->addPost($this);
            $tag->getTranslations()
                ->filter(fn (TagTranslation $translation) => $translation->getLanguage() === $this->getLanguage())
                ->first()
                ->incrementCount();
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        /** @var Post $this */
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
            $tag->removePost($this);
            $tag->getTranslation($this->getLanguage())->decrementCount();
        }

        return $this;
    }

    public function syncTags(Collection $items): self
    {
        foreach ($this->getForRemoval('tags', $items) as $item) {
            $this->removeTag($item);
        }

        foreach ($items as $item) {
            $this->addTag($item);
        }

        return $this;
    }
}