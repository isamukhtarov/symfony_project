<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Entity\Post\Traits;

use Doctrine\Common\Collections\Collection;
use Ria\Bundle\PostBundle\Entity\Post\Post;
use Doctrine\ORM\Mapping\{JoinColumn, JoinTable, ManyToMany};

trait Related
{
    /**
     * @ManyToMany(targetEntity="Ria\Bundle\PostBundle\Entity\Post\Post")
     * @JoinTable(name="post_related",
     *     joinColumns={@JoinColumn(name="post_id", referencedColumnName="id", onDelete="CASCADE")},
     *     inverseJoinColumns={@JoinColumn(name="related_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    private Collection $relatedPosts;

    public function getRelatedPosts(): Collection
    {
        return $this->relatedPosts;
    }

    public function addRelated(Post $post): self
    {
        if (!$this->relatedPosts->contains($post))
            $this->relatedPosts->add($post);

        return $this;
    }

    public function removeRelated(Post $post): self
    {
        if ($this->relatedPosts->contains($post))
            $this->relatedPosts->removeElement($post);

        return $this;
    }
}
