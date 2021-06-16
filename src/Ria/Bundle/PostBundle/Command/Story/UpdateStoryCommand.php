<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Command\Story;

use Ria\Bundle\PhotoBundle\Entity\Photo;
use Ria\Bundle\PostBundle\Entity\Story\Story;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateStoryCommand
{
    #[Assert\Type('boolean')]
    public bool $status;

    #[Assert\Type('boolean')]
    public bool $showOnSite;

    public Photo|int|null $cover;

    #[Assert\Valid]
    public array $translations;

    private Story $story;

    public function __construct(Story $story, array $locales)
    {
        $this->story = $story;
        $this->status = $story->getStatus();
        $this->showOnSite = $story->isShownOnSite();
        $this->cover = $story->getCover();

        foreach ($locales as $locale)
            $this->translations[$locale] = new StoryTranslationCommand($locale, $story->getTranslation($locale));
    }

    public function getStory(): Story
    {
        return $this->story;
    }
}