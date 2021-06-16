<?php

declare(strict_types=1);

namespace Ria\Bundle\UserBundle\Handler\User;

use JetBrains\PhpStorm\NoReturn;
use Ria\Bundle\UserBundle\Repository\UserRepository;
use Ria\Bundle\UserBundle\Command\User\ChangeUserPasswordCommand;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ChangeUserPasswordHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordEncoderInterface $passwordEncoder,
    ){}

    #[NoReturn] public function handle(ChangeUserPasswordCommand $command): void
    {
        $user = $this->userRepository->find($command->getUser()->getId());
        $this->userRepository->upgradePassword(
            $user, $this->passwordEncoder->encodePassword($user, $command->password)
        );
    }
}