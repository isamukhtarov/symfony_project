<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Handler\Widget;

use Doctrine\ORM\EntityManagerInterface;
use Ria\Bundle\PostBundle\Command\Widget\CreateWidgetCommand;
use Ria\Bundle\PostBundle\Entity\Widget\Widget;

class CreateWidgetHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ){}

    public function handle(CreateWidgetCommand $command): void
    {
        $widget = new Widget();

        $widget->setContent($command->getContent())
               ->setType($command->getType());

        $this->entityManager->persist($widget);
        $this->entityManager->flush();

        $command->id = $widget->getId();
    }
}