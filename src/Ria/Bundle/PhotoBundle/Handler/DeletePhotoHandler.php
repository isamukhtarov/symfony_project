<?php
declare(strict_types=1);

namespace Ria\Bundle\PhotoBundle\Handler;

use Doctrine\ORM\EntityManagerInterface;
use Ria\Bundle\PhotoBundle\Command\DeletePhotoCommand;
use Ria\Bundle\PhotoBundle\Entity\Photo;
use Ria\Bundle\PhotoBundle\Service\ImageManipulatorInterface;

class DeletePhotoHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ImageManipulatorInterface $imageManipulator,
    ) {}

    public function handle(DeletePhotoCommand $command): void
    {
        $photo = $this->entityManager->find(Photo::class, $command->id);
        $filename = $photo->getFilename();

        $this->entityManager->remove($photo);
        $this->entityManager->flush();

        $this->imageManipulator->delete($filename);
    }

}