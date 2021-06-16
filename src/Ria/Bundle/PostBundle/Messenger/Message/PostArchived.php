<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Messenger\Message;

class PostArchived
{
    public function __construct(
        private int $postId,
        private string $link,
    ){}

    public function getPostId(): int
    {
        return $this->postId;
    }

    public function getLink(): string
    {
        return $this->link;
    }
}