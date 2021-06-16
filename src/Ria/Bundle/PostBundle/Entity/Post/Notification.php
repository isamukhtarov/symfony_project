<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Entity\Post;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;

/**
 * @ORM\Table(name="post_notifications")
 * @ORM\Entity
 */
class Notification
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\OneToOne(targetEntity="Ria\Bundle\PostBundle\Entity\Post\Post", inversedBy="notification")
     * @JoinColumn(name="post_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private Post $post;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $status;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     */
    private DateTime $createdAt;

    public function getId(): int
    {
        return $this->id;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getStatus(): bool
    {
        return $this->status;
    }

    public function setPost(Post $post): self
    {
        $this->post = $post;

        return $this;
    }

    public function getPost(): Post
    {
        return $this->post;
    }

    public function setCreatedAt($createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }
}