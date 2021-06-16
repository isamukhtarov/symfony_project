<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Handler\Speech;

use Doctrine\ORM\EntityManagerInterface;
use Ria\Bundle\PostBundle\Command\Speech\CreateSpeechCommand;
use Ria\Bundle\PostBundle\Entity\Post\Post;
use Ria\Bundle\PostBundle\Entity\Post\Speech;
use Ria\Bundle\PostBundle\Service\Speech\SpeechManipulatorInterface;

class CreateSpeechHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SpeechManipulatorInterface $speechManipulator
    ){}


    public function handle(CreateSpeechCommand $command): void
    {
        $speech = new Speech();
        $post = $this->entityManager->find(Post::class, $command->postId);
        $duration = $command->getFileDurationInSeconds($command->file->getPathname());

        $speech->setOriginalFilename(pathinfo($command->file->getClientOriginalName(), PATHINFO_FILENAME))
               ->setPost($post)
               ->setSize(filesize($command->file->getPathname()))
               ->setDuration($duration);

        $speechName = $this->speechManipulator->upload($command->file);
        $speech->setFilename($speechName);

        $this->entityManager->persist($speech);
        $this->entityManager->flush();
    }
}