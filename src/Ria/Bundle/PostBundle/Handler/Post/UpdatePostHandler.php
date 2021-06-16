<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Handler\Post;

use Ria\Bundle\PostBundle\Dto\PlainPostDto;
use Ria\Bundle\PostBundle\Command\Post\UpdatePostCommand;
use Ria\Bundle\PostBundle\Messenger\Message\{PostUpdated, PostSaved};

class UpdatePostHandler extends AbstractPostHandler
{
    public function handle(UpdatePostCommand $command)
    {
        $post = $command->getPostDto()->getPost();
        $user = $command->getPostDto()->getUser();
        $oldVersionOfPost = PlainPostDto::fromEntity($post);
        $actualVersionOfPost = $this->persistEntity($post, $command);
        $this->messageBus->dispatch(new PostUpdated($actualVersionOfPost->getId(), $user->getId(), $oldVersionOfPost));
        $this->messageBus->dispatch(new PostSaved($actualVersionOfPost->getId(), $user->getId()));
    }
}