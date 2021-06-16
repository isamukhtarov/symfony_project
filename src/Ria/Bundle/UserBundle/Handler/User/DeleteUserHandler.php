<?php

declare(strict_types=1);

namespace Ria\Bundle\UserBundle\Handler\User;

use Ria\Bundle\UserBundle\Repository\UserRepository;
use Ria\Bundle\UserBundle\Command\User\DeleteUserCommand;

class DeleteUserHandler
{
    public function __construct(
        private UserRepository $userRepository,
    ){}

    public function handle(DeleteUserCommand $command)
    {
        $this->userRepository->remove($this->userRepository->find($command->getUser()->getId()));
    }
}
