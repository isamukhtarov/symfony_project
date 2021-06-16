<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Handler\Post;

use Ria\Bundle\PostBundle\Entity\Post\Status;
use Symfony\Component\Messenger\MessageBusInterface;
use Ria\Bundle\PostBundle\Repository\PostRepository;
use Ria\Bundle\PostBundle\Messenger\Message\PostDeleted;
use Ria\Bundle\PostBundle\Command\Post\DeletePostCommand;

class DeletePostHandler
{
    public function __construct(
        private PostRepository $postRepository,
        private MessageBusInterface $messageBus,
    ){}

    public function handle(DeletePostCommand $command)
    {
        $post = $command->getPost();
        $post->setStatus(new Status($command->status))
            ->setIsPublished(false);
        $this->postRepository->save($post);

        // Flush cache here.

        $this->messageBus->dispatch(new PostDeleted($post->getId(), $command->getUser()->getId(), $command->cause));
    }
}