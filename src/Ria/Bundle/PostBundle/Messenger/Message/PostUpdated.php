<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Messenger\Message;

use Ria\Bundle\PostBundle\Dto\PlainPostDto;

class PostUpdated
{
    public function __construct(
        private int $postId,
        private int $userId,
        private PlainPostDto $plainPostDto,
    ){}

    public function getPostId(): int
    {
        return $this->postId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getPlainPostDto(): PlainPostDto
    {
        return $this->plainPostDto;
    }
}