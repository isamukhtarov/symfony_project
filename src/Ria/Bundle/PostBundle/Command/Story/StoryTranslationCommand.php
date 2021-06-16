<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Command\Story;

use Ria\Bundle\AdminBundle\Command\MetaCommand;
use Ria\Bundle\PostBundle\Entity\Story\Translation;
use Symfony\Component\Validator\Constraints as Assert;

class StoryTranslationCommand
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Length(max: 255)]
    public string $title;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Length(max: 255)]
    public string $slug;

    #[Assert\Type('string')]
    public ?string $description;

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
            $this->description = $translation->getDescription();
            $this->meta = new MetaCommand($translation->getMeta());
        } else {
            $this->meta = new MetaCommand();
        }
    }
}