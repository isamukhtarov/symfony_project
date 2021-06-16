<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Command\Story;

use JetBrains\PhpStorm\Pure;
use Symfony\Component\Validator\Constraints as Assert;

class CreateStoryCommand
{
    #[Assert\Type('boolean')]
    public bool $status;

    #[Assert\Type('boolean')]
    public bool $showOnSite;

    public ?int $cover = null;

    #[Assert\Valid]
    public array $translations;

    #[Pure] public function __construct(array $locales)
    {
        foreach ($locales as $locale)
            $this->translations[$locale] = new CreateStoryTranslationCommand($locale);
    }
}