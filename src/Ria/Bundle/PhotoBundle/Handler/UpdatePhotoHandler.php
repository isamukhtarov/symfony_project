<?php
declare(strict_types=1);

namespace Ria\Bundle\PhotoBundle\Handler;

use Doctrine\ORM\EntityManagerInterface;
use Ria\Bundle\PhotoBundle\Command\PhotoTranslationCommand;
use Ria\Bundle\PhotoBundle\Command\UpdatePhotoCommand;
use Ria\Bundle\PhotoBundle\Entity\Photo;
use Ria\Bundle\PhotoBundle\Entity\Translation;

class UpdatePhotoHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function handle(UpdatePhotoCommand $command)
    {
        /** @var Photo $photo */
        $photo = $this->entityManager->find(Photo::class, $command->id);

        foreach ($command->translations as $translationCommand) {
            /** @var PhotoTranslationCommand $translationCommand */
            $translation = $photo->getTranslation($translationCommand->locale) ?: new Translation();

            $translation
                ->setLanguage($translationCommand->locale)
                ->setInformation($translationCommand->information)
                ->setAuthor($translationCommand->author)
                ->setSource($translationCommand->source);

            $photo->addTranslation($translation);
        }

        $this->entityManager->persist($photo);
        $this->entityManager->flush();
    }
}