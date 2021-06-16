<?php

declare(strict_types=1);

namespace Ria\Bundle\UserBundle\Command\User;

use Ria\Bundle\UserBundle\Entity\User;

class UpdateUserCommand extends AbstractUserCommand
{
    private User $user;

    public function __construct(
        User $user,
        array $roles,
        array $permissions,
        array $locales
    )
    {
        parent::__construct($roles, $permissions);

        $this->user = $user;

        $this->email = $user->getEmail();
        $this->emailAdditional = $user->getEmailAdditional();
        $this->gender = $user->getGender();
        $this->birthDate = $user->getBirthDate();
        $this->phone = $user->getPhone();
        $this->status = $user->getStatus()->toValue();
        $this->photo = $user->getPhoto();

        foreach ($locales as $locale)
            $this->translations[$locale] = new UserTranslationCommand($locale, $user->getTranslation($locale));
    }

    public function getUser(): User
    {
        return $this->user;
    }
}