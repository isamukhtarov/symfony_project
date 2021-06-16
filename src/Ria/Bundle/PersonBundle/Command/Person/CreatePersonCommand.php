<?php
declare(strict_types=1);

namespace Ria\Bundle\PersonBundle\Command\Person;

use Ria\Bundle\PersonBundle\Entity\Person\Type;
use Ria\Bundle\PhotoBundle\Command\PhotoCommand;
use Symfony\Component\Validator\Constraints as Assert;

class CreatePersonCommand
{
    #[Assert\Type('boolean')]
    public bool $status;

    #[Assert\Valid]
    public PhotoCommand $photos;

    public int|null $photo = null;

    #[Assert\Valid]
    public array $translations;

    public function __construct($type, array $locales)
    {
        $this->type = new Type($type);
        $this->photos = new PhotoCommand();

        foreach ($locales as $locale) {
            $this->translations[$locale] = new CreatePersonTranslationCommand($locale);
        }
    }
}