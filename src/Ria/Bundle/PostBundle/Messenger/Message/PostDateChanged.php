<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Messenger\Message;

class PostDateChanged
{
    public function __construct(
        private int $postId,
        private string $cause,
    ){}

    public function getPostId(): int
    {
        return $this->postId;
    }

    public function getCause(): string
    {
        return $this->cause;
    }
}