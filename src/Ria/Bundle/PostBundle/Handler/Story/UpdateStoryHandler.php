<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Handler\Story;

use Ria\Bundle\CoreBundle\Entity\Meta;
use Ria\Bundle\PhotoBundle\Entity\Photo;
use Doctrine\ORM\EntityManagerInterface;
use Ria\Bundle\PostBundle\Entity\Story\Story;
use Ria\Bundle\PostBundle\Command\Story\UpdateStoryCommand;

class UpdateStoryHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ){}

    public function handle(UpdateStoryCommand $command): void
    {
        $story = $this->entityManager->find(Story::class, $command->getStory()->getId());

        $story->setStatus($command->status)
            ->setShowOnSite($command->showOnSite)
            ->setCover(
                $command->cover ? $this->entityManager->getPartialReference(Photo::class, $command->cover) : null
            );

        foreach ($command->translations as $translationCommand) {
            $translation = $story->getTranslation($translationCommand->locale);
            $translation
                ->setTitle($translationCommand->title)
                ->setSlug($translationCommand->slug)
                ->setDescription($translationCommand->description ?? '')
                ->setLanguage($translationCommand->locale)
                ->setMeta(new Meta(...(array) $translationCommand->meta));

            $story->addTranslation($translation);
        }

        $this->entityManager->persist($story);
        $this->entityManager->flush();
    }
}