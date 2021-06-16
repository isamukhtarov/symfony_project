<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Command\Category;

use Ria\Bundle\AdminBundle\Command\MetaCommand;
use Ria\Bundle\PostBundle\Entity\Category\Translation;
use Symfony\Component\Validator\Constraints as Assert;

class CategoryTranslationCommand
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

    #[Assert\Valid]
    public MetaCommand $meta;

    public function __construct(string $locale, ?Translation $translation = null)
    {
        $this->locale = $locale;

        if ($translation) {
            $this->title = $translation->getTitle();
            $this->slug = $translation->getSlug();
            $this->meta = new MetaCommand($translation->getMeta());
        } else {
            $this->meta = new MetaCommand();
        }
    }
}