<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Handler\Post;

use Ria\Bundle\PostBundle\Command\Post\CancelPostCommand;
use Ria\Bundle\PostBundle\Messenger\Message\PostCanceled;
use Symfony\Component\Messenger\MessageBusInterface;

class CancelPostHandler
{
    public function __construct(
        private MessageBusInterface $messageBus
    ){}

    public function handle(CancelPostCommand $command): void
    {
        $post = $command->getPost();

        $this->messageBus->dispatch(new PostCanceled($post->getId(), $command->getUser()->getId()));
    }
}