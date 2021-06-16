<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Messenger\Subscriber;

use Ria\Bundle\PostBundle\Entity\Post\Status;
use Ria\Bundle\PostBundle\Repository\PostRepository;
use Ria\Bundle\PostBundle\Messenger\Message\PostUpdated;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class PostUpdatedSubscriber implements MessageSubscriberInterface
{
    public function __construct(
        private PostRepository $postRepository,
    ){}

    public function __invoke(PostUpdated $message)
    {
        $actualPost = $this->postRepository->find($message->getPostId());
        $oldPost = $message->getPlainPostDto();

        if ($actualPost->getStatus()->toValue() === $oldPost->getStatus()) return;
        // Nothing was changed, so, call the next handler.

        $isDeactivated = in_array($oldPost->getStatus(), Status::activeOnes()) &&
            !in_array($actualPost->getStatus()->toValue(), Status::activeOnes());

        $actualPost->setIsDeactivated($isDeactivated);
    }

    public static function getHandledMessages(): iterable
    {
        yield PostUpdated::class => ['priority' => 100];
    }
}