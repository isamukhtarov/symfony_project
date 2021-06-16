<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Messenger\Message;

class PostDeleted
{
    public function __construct(
        private int $postId,
        private int $userId,
        private string $cause,
    ){}

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getPostId(): int
    {
        return $this->postId;
    }

    public function getCause(): string
    {
        return $this->cause;
    }
}