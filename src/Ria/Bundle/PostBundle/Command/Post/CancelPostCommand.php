<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Command\Post;

use Ria\Bundle\PostBundle\Entity\Post\Post;
use Ria\Bundle\UserBundle\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;

class CancelPostCommand
{
    public function __construct(
        private Post $post,
        private UserInterface $user
    ){}

    public function getPost(): Post
    {
        return $this->post;
    }

    public function getUser(): UserInterface|User
    {
        return $this->user;
    }
}