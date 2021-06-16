<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Handler\Speech;

use Doctrine\ORM\EntityManagerInterface;
use Ria\Bundle\PostBundle\Command\Speech\UpdateSpeechCommand;
use Ria\Bundle\PostBundle\Entity\Post\Speech;
use Ria\Bundle\PostBundle\Service\Speech\SpeechManipulatorInterface;

class UpdateSpeechHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SpeechManipulatorInterface $speechManipulator
    ){}

    public function handle(UpdateSpeechCommand $command): void
    {
        $speech = $this->entityManager->find(Speech::class, $command->id);
        $duration = $command->getFileDurationInSeconds($command->file->getPathname());

        $speech
            ->setOriginalFilename(pathinfo($command->file->getClientOriginalName(), PATHINFO_FILENAME))
            ->setSize(filesize($command->file->getPathname()))
            ->setDuration($duration);

        $this->speechManipulator->delete($speech->getFilename());

        $fileName = $this->speechManipulator->upload($command->file);
        $speech->setFilename($fileName);

        $this->entityManager->flush();
    }
}