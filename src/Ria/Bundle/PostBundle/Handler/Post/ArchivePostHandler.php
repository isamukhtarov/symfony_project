<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Handler\Post;

use Ria\Bundle\PostBundle\Entity\Post\Status;
use Ria\Bundle\PostBundle\Repository\PostRepository;
use Symfony\Component\Messenger\MessageBusInterface;
use Ria\Bundle\PostBundle\Messenger\Message\PostArchived;
use Ria\Bundle\PostBundle\Command\Post\ArchivePostCommand;

class ArchivePostHandler
{
    public function __construct(
        private PostRepository $postRepository,
        private MessageBusInterface $messageBus,
    ){}

    public function handle(ArchivePostCommand $command): void
    {
        $post = $command->getPost();
        $prevStatus = $post->getStatus();
        $post->setStatus(new Status(Status::ARCHIVED));
        $post->setIsPublished(false);
        $this->postRepository->save($post);

        if ($prevStatus->isActive())
            $this->messageBus->dispatch(new PostArchived($post->getId(), $command->link));
    }
}