<?php
declare(strict_types=1);

namespace Ria\Bundle\PhotoBundle\Command;

use JetBrains\PhpStorm\Pure;
use Ria\Bundle\PhotoBundle\Entity\Photo;
use Symfony\Component\Validator\Constraints as Assert;

class UpdatePhotoCommand
{
    public int $id;

    #[Assert\Valid]
    public array $translations;

    #[Pure] public function __construct(Photo $photo, array $locales)
    {
        $this->id = $photo->getId();

        foreach ($locales as $locale) {
            $this->translations[$locale] = new PhotoTranslationCommand($locale, $photo->getTranslation($locale));
        }
    }

}