<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Command\Post;

use Ria\Bundle\UserBundle\Entity\User;
use Ria\Bundle\PostBundle\Entity\Post\Post;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Ria\Bundle\CoreBundle\Component\CommandValidator\Validatable;

class DeletePostCommand implements Validatable
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    public ?string $cause;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    public ?string $status;

    public function __construct(
        private Post $post,
        private UserInterface $user,
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