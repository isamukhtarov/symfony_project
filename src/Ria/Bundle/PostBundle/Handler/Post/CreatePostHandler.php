<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Handler\Post;

use Ria\Bundle\PostBundle\Entity\Post\Post;
use Ria\Bundle\PostBundle\Command\Post\CreatePostCommand;
use Ria\Bundle\PostBundle\Messenger\Message\{PostCreated, PostSaved};

class CreatePostHandler extends AbstractPostHandler
{
    public function handle(CreatePostCommand $command): void
    {
        $post = $this->persistEntity(new Post(), $command);
        $user = $command->getPostDto()->getUser();
        $this->messageBus->dispatch(new PostCreated($post->getId(), $user->getId()));
        $this->messageBus->dispatch(new PostSaved($post->getId(), $user->getId()));
    }
}