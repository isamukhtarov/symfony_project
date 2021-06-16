<?php

declare(strict_types=1);

namespace Ria\Bundle\UserBundle\Command\User;

use Ria\Bundle\AdminBundle\Command\MetaCommand;
use Symfony\Component\Validator\Constraints as Assert;
use Ria\Bundle\UserBundle\Entity\Translation as UserTranslation;

class UserTranslationCommand
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Length(max: 100)]
    public string $firstName;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Length(max: 100)]
    public string $lastName;

    #[Assert\Type('string')]
    public ?string $position = null;

    #[Assert\Type('string')]
    public ?string $description = null;

    #[Assert\NotBlank]
    public string $locale;

    #[Assert\Valid]
    public MetaCommand $meta;

    public function __construct(string $locale, ?UserTranslation $translation = null)
    {
        $this->locale = $locale;

        if ($translation) {
            $this->firstName = $translation->getFirstName();
            $this->lastName = $translation->getLastName();
            $this->position = $translation->getPosition();
            $this->description = $translation->getDescription();
            $this->meta = new MetaCommand($translation->getMeta());
        } else {
            $this->meta = new MetaCommand();
        }
    }
}
