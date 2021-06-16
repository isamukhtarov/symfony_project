<?php

declare(strict_types=1);

namespace Ria\Bundle\UserBundle\Handler\User;

use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\NoReturn;
use Ria\Bundle\CoreBundle\Entity\Meta;
use Ria\Bundle\CoreBundle\Entity\Status;
use Ria\Bundle\PhotoBundle\Entity\Photo;
use Ria\Bundle\UserBundle\Entity\User;
use Ria\Bundle\UserBundle\Repository\UserRepository;
use Symfony\Component\String\Slugger\SluggerInterface;
use Ria\Bundle\UserBundle\Command\User\CreateUserCommand;
use Ria\Bundle\UserBundle\Entity\Translation as UserTranslation;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateUserHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordEncoderInterface $passwordEncoder,
        private EntityManagerInterface $entityManager,
        private SluggerInterface $slugger
    ){}

    #[NoReturn] public function handle(CreateUserCommand $command): void
    {
        $user = new User();
        $user->setEmail($command->email)
            ->setEmailAdditional($command->emailAdditional)
            ->setGender($command->gender)
            ->setPhone($command->phone)
            ->setBirthDate($command->birthDate)
            ->setStatus(new Status($command->status))
            ->setPassword($this->passwordEncoder->encodePassword($user, $command->password))
            ->sync('roles', $command->getRoles(true))
            ->sync('permissions', $command->getPermissions(true));

        /** @var Photo|null $mainPhoto */
        $mainPhoto = $command->photo ? $this->entityManager->getReference(Photo::class, $command->photo) : null;
        $user->setPhoto($mainPhoto);

        // Setting translations for user.
        foreach ($command->translations as $translationCommand) {
            $translation = new UserTranslation();

            $translation
                ->setFirstName($translationCommand->firstName)
                ->setLastName($translationCommand->lastName)
                ->setPosition($translationCommand->position)
                ->setDescription($translationCommand->description)
                ->setLanguage($translationCommand->locale)
                ->setMeta(new Meta(...(array) $translationCommand->meta));

            $slug = $this->slugger->slug(mb_strtolower($translation->getFullName(), 'UTF-8'));
            $translation->setSlug($slug->toString());

            $user->addTranslation($translation);
        }

        $this->userRepository->save($user);
    }
}
