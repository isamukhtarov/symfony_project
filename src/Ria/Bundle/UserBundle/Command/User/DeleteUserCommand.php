<?php

declare(strict_types=1);

namespace Ria\Bundle\UserBundle\Command\User;

use Ria\Bundle\UserBundle\Entity\User;

class DeleteUserCommand
{
    public function __construct(
        private User $user
    ){}

    public function getUser(): User
    {
        return $this->user;
    }
}
