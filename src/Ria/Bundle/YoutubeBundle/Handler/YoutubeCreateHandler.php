<?php

declare(strict_types=1);

namespace Ria\Bundle\YoutubeBundle\Handler;

use Doctrine\ORM\EntityManagerInterface;
use Ria\Bundle\YoutubeBundle\Command\YoutubeCreateCommand;
use Ria\Bundle\YoutubeBundle\Entity\YouTube;

class YoutubeCreateHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {}

    public function handle(YoutubeCreateCommand $command): void
    {
        $youtube = new YouTube();

        $youtube
            ->setYoutubeId($command->youtubeId)
            ->setStatus($command->status);

        $this->entityManager->persist($youtube);
        $this->entityManager->flush();
    }
}