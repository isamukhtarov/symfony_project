<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Messenger\Message;

class PostCanceled
{
    public function __construct(
        private int $postId,
        private int $userId
    ){}

    public function getPostId(): int
    {
        return $this->postId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
}