<?php
declare(strict_types=1);

namespace Ria\Bundle\PhotoBundle\Command;

use JetBrains\PhpStorm\Pure;
use Ria\Bundle\PhotoBundle\Entity\Translation;
use Symfony\Component\Validator\Constraints as Assert;

class PhotoTranslationCommand
{
    #[Assert\Type('string')]
    #[Assert\Length(max: 255)]
    public string|null $author = null;

    #[Assert\Type('string')]
    #[Assert\Length(max: 255)]
    public string|null $information = null;

    #[Assert\Type('string')]
    #[Assert\Length(max: 255)]
    public string|null $source = null;

    #[Assert\NotBlank]
    public string|null $locale;

    #[Pure] public function __construct(string $locale, Translation $translation = null)
    {
        $this->locale = $locale;

        if ($translation) {
            $this->author = $translation->getAuthor();
            $this->information = $translation->getInformation();
            $this->source = $translation->getSource();
        }
    }
}