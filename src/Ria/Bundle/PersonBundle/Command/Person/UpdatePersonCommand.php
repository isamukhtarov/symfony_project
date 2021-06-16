<?php
declare(strict_types=1);

namespace Ria\Bundle\PersonBundle\Command\Person;

use Ria\Bundle\PersonBundle\Entity\Person\Person;
use Ria\Bundle\PhotoBundle\Command\PhotoCommand;
use Ria\Bundle\PhotoBundle\Entity\Photo;
use Symfony\Component\Validator\Constraints as Assert;

class UpdatePersonCommand
{
    public int $id;

    #[Assert\Type('boolean')]
    public bool $status;

    #[Assert\Valid]
    public PhotoCommand $photos;

    public Photo|int|null $photo;

    #[Assert\Valid]
    public array $translations;

    public function __construct(Person $person, array $locales)
    {
        $this->id = $person->getId();
        $this->status = $person->getStatus();
        $this->photos = new PhotoCommand(
            $person->getPhotos()->map(fn($photo) => $photo->getId())->toArray(),
            $person->getPhoto()?->getId()
        );

        $this->photo = $person->getPhoto();

        foreach ($locales as $locale) {
            $this->translations[$locale] = new PersonTranslationCommand($locale, $person->getTranslation($locale));
        }
    }
}