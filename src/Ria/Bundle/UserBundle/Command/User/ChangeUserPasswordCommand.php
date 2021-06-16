<?php

declare(strict_types=1);

namespace Ria\Bundle\UserBundle\Command\User;

use Ria\Bundle\UserBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

class ChangeUserPasswordCommand
{
    #[Assert\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Length(min: 5, max: 255)]
    public string $password;

    public function __construct(
        private User $user
    ){}

    public function getUser(): User
    {
        return $this->user;
    }
}