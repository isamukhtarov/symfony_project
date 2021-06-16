<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Dto;

use Ria\Bundle\UserBundle\Entity\User;
use Ria\Bundle\PostBundle\Entity\Post\Note;
use Ria\Bundle\PostBundle\Entity\Post\Post;
use Symfony\Component\Security\Core\User\UserInterface;

class PostDto
{
    public function __construct(
        private UserInterface $user,
        private string $language,
        private ?Post $post = null,
        private ?Note $note = null,
    ){}

    public function getUser(): UserInterface|User
    {
        return $this->user;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function getNote(): ?Note
    {
        return $this->note;
    }

    public function hasPost(): bool
    {
        return $this->post !== null;
    }
}