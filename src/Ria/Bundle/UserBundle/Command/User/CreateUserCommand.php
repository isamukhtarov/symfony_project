<?php

declare(strict_types=1);

namespace Ria\Bundle\UserBundle\Command\User;

use Ria\Bundle\UserBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;
use Ria\Bundle\CoreBundle\Validation\Constraint\CommandUniqueEntity;

#[CommandUniqueEntity([
    'entityClass' => User::class,
    'fieldMapping' => ['email' => 'email']
])]
class CreateUserCommand extends AbstractUserCommand
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Length(min: 5, max: 255)]
    public string $password;

    public function __construct(array $roles, array $permissions, array $locales)
    {
        parent::__construct($roles, $permissions);

        foreach ($locales as $locale)
            $this->translations[$locale] = new UserTranslationCommand($locale);
    }
}