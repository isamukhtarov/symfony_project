<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Entity\Post;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="post_notes")
 * @ORM\Entity
 */
class Note
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
    private string $body;

    /**
     * @ORM\Column(type="integer", name="post_group_id", options={"unsigned"=true})
     */
    private int $postGroupId;

    public function setPostGroupId(int $groupId): self
    {
        $this->postGroupId = $groupId;

        return $this;
    }

    public function getPostGroupId(): int
    {
        return $this->postGroupId;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getBody(): string
    {
        return $this->body;
    }
}
