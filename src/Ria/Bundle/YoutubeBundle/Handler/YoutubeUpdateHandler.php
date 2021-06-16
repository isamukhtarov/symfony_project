<?php

declare(strict_types=1);

namespace Ria\Bundle\YoutubeBundle\Handler;


use Doctrine\ORM\EntityManagerInterface;
use Ria\Bundle\YoutubeBundle\Command\YoutubeUpdateCommand;
use Ria\Bundle\YoutubeBundle\Entity\YouTube;

class YoutubeUpdateHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ){}

    public function handle(YoutubeUpdateCommand $command): void
    {
        $youtube = $this->entityManager->find(YouTube::class, $command->id);

        $youtube
            ->setYoutubeId($command->youtubeId)
            ->setStatus($command->status);

        $this->entityManager->flush();
    }
}