<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Command\Region;

use JetBrains\PhpStorm\Pure;
use Ria\Bundle\PostBundle\Entity\Region\Translation;
use Symfony\Component\Validator\Constraints as Assert;

class RegionTranslationCommand
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Length(max: 255)]
    public string $title;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Length(max: 255)]
    public string $slug;

    #[Assert\NotBlank]
    public string $locale;

    #[Pure] public function __construct(string $locale, ?Translation $translation = null)
    {
        $this->locale = $locale;

        if ($translation) {
            $this->title = $translation->getTitle();
            $this->slug = $translation->getSlug();
        }
    }
}