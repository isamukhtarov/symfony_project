<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Handler\Story;

use DateTime;
use Ria\Bundle\CoreBundle\Entity\Meta;
use Ria\Bundle\PhotoBundle\Entity\Photo;
use Doctrine\ORM\EntityManagerInterface;
use Ria\Bundle\PostBundle\Entity\Story\Story;
use Ria\Bundle\PostBundle\Command\Story\CreateStoryCommand;
use Ria\Bundle\PostBundle\Entity\Story\Translation as StoryTranslation;

class CreateStoryHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ){}

    public function handle(CreateStoryCommand $command): void
    {
        $story = (new Story())
            ->setStatus($command->status)
            ->setShowOnSite($command->showOnSite)
            ->setCreatedAt(new DateTime());

        if ($command->cover) {
            /** @var Photo|null $cover */
            $cover = $this->entityManager->getPartialReference(Photo::class, $command->cover);
            $story->setCover($cover);
        }

        foreach ($command->translations as $translationCommand) {
            $story->addTranslation((new StoryTranslation())
                ->setTitle($translationCommand->title)
                ->setSlug($translationCommand->slug)
                ->setDescription($translationCommand->description ?? '')
                ->setLanguage($translationCommand->locale)
                ->setMeta(new Meta(...(array) $translationCommand->meta)));
        }

        $this->entityManager->persist($story);
        $this->entityManager->flush();
    }
}