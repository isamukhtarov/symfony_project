<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Entity\Post\Log;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Ria\Bundle\UserBundle\Entity\User;
use Ria\Bundle\PostBundle\Entity\Post\Post;

/**
 * @ORM\Table(name="post_logs")
 * @ORM\Entity
 */
class Log
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Embedded(class="Type", columnPrefix=false)
     */
    private Type $type;

    /**
     * @ORM\ManyToOne(targetEntity="Ria\Bundle\PostBundle\Entity\Post\Post", inversedBy="logs")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private Post $post;

    /**
     * @ORM\ManyToOne(targetEntity="Ria\Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private ?User $user;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $snapshot;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     */
    private DateTime $createdAt;

    public function getId(): int
    {
        return $this->id;
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

    public function getPost(): Post
    {
        return $this->post;
    }

    public function setPost(Post $post): self
    {
        $this->post = $post;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getSnapshot(): ?string
    {
        return $this->snapshot;
    }

    public function setSnapshot(?string $snapshot): self
    {
        $this->snapshot = $snapshot;

        return $this;
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