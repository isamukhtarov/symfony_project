<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Entity\Post;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;

/**
 * @ORM\Entity
 * @ORM\Table(name="post_speech")
 */
class Speech
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=42, nullable=false)
     */
    private string $filename;

    /**
     * @ORM\Column(type="string", nullable=true, length=255, options={"default":null})
     */
    private ?string $originalFilename;

    /**
     * @ORM\Column(type="integer", options={"default":0})
     */
    private int $duration;

    /**
     * @ORM\Column(type="integer", options={"default":0})
     */
    private int $size;

    /**
     * @ORM\OneToOne(targetEntity="Ria\Bundle\PostBundle\Entity\Post\Post", inversedBy="speech")
     * @JoinColumn(name="post_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private Post $post;

    /**
     * @ORM\Column(type="datetime", name="created_at", columnDefinition="TIMESTAMP DEFAULT CURRENT_TIMESTAMP")
     */
    private DateTime $createdAt;

    /**
     * @ORM\Column(type="datetime", name="updated_at", columnDefinition="TIMESTAMP DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP")
     */
    private DateTime $updatedAt;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     * @return self
     */
    public function setFilename(string $filename): self
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getOriginalFilename(): ?string
    {
        return $this->originalFilename;
    }

    /**
     * @param string|null $originalFilename
     * @return Speech
     */
    public function setOriginalFilename(?string $originalFilename): self
    {
        $this->originalFilename = $originalFilename;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getDuration(): ?int
    {
        return $this->duration;
    }

    /**
     * @param int $duration
     * @return Speech
     */
    public function setDuration(int $duration): self
    {
        $this->duration = $duration;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getSize(): ?int
    {
        return $this->size;
    }

    /**
     * @param int $size
     * @return Speech
     */
    public function setSize(int $size): self
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @return Post
     */
    public function getPost(): Post
    {
        return $this->post;
    }

    /**
     * @param Post $post
     * @return Speech
     */
    public function setPost(Post $post): self
    {
        $this->post = $post;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTime $updatedAt
     * @return $this
     */
    public function setUpdatedAt(DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

}