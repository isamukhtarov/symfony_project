<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Handler\Story;

use Ria\Bundle\PostBundle\Command\Story\DeleteStoryCommand;
use Ria\Bundle\PostBundle\Query\Repository\StoryRepository;

class DeleteStoryHandler
{
    public function __construct(
        private StoryRepository $storyRepository,
    ){}

    public function handle(DeleteStoryCommand $command): void
    {
        if (($story = $this->storyRepository->find($command->getId())) === null) return;
        $this->storyRepository->remove($story);
    }
}