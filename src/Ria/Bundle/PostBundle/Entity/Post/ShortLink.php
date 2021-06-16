<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Entity\Post;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;

/**
 * @ORM\Entity
 * @ORM\Table(name="post_short_links")
 */
class ShortLink
{

    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    private string $hash;

    /**
     * @ORM\Column(type="string", name="redirect_to")
     */
    private string $redirectTo = '';

    /**
     * @ORM\Column(type="integer")
     */
    private int $transits = 0;

    /**
     * @ORM\OneToOne(targetEntity="Ria\Bundle\PostBundle\Entity\Post\Post")
     * @JoinColumn(name="post_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private Post $post;

    public function getHash(): string
    {
        return $this->hash;
    }

    public function setHash(string $hash): self
    {
        $this->hash = $hash;

        return $this;
    }

    public function getRedirectTo(): string
    {
        return $this->redirectTo;
    }

    public function setRedirectTo(string $redirectTO): self
    {
        $this->redirectTo = $redirectTO;

        return $this;
    }

    public function getTransits(): int
    {
        return $this->transits;
    }

    public function getPost(): Post
    {
        return $this->post;
    }

    public function setPost(Post $post): self
    {
        $this->post = $post;

        return $this;
    }

    public function incrementTransits(): void
    {
        $this->transits++;
    }

    public function setTransits(int $transits): self
    {
        $this->transits = $transits;

        return $this;
    }
}