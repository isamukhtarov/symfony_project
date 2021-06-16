<?php

declare(strict_types=1);

namespace Ria\Bundle\YoutubeBundle\Handler;

use Doctrine\ORM\EntityManagerInterface;
use Ria\Bundle\YoutubeBundle\Command\YoutubeDeleteCommand;
use Ria\Bundle\YoutubeBundle\Entity\YouTube;

class YoutubeDeleteHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ){}

    public function handle(YoutubeDeleteCommand $command): void
    {
        $youtube = $this->entityManager->find(YouTube::class, $command->getId());

        $this->entityManager->remove($youtube);
        $this->entityManager->flush();
    }
}