<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Handler\Speech;

use Doctrine\ORM\EntityManagerInterface;
use Ria\Bundle\PostBundle\Command\Speech\DeleteSpeechCommand;
use Ria\Bundle\PostBundle\Entity\Post\Speech;
use Ria\Bundle\PostBundle\Service\Speech\SpeechManipulatorInterface;

class DeleteSpeechHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SpeechManipulatorInterface $speechManipulator
    ){}

    public function handle(DeleteSpeechCommand $command): void
    {
        $speech = $this->entityManager->find(Speech::class, $command->getId());
        $filename = $speech->getFilename();

        $this->entityManager->remove($speech);
        $this->entityManager->flush();

        $this->speechManipulator->delete($filename);
    }
}