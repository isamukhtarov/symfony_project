<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Messenger\Subscriber;

use Exception;
use Psr\Log\LoggerInterface;
use Ria\Bundle\PostBundle\Query\PostIndexer;
use Ria\Bundle\PostBundle\Repository\PostRepository;
use Ria\Bundle\PostBundle\Messenger\Message\PostSaved;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class ReIndexPostSubscriber implements MessageSubscriberInterface
{
    public function __construct(
        private LoggerInterface $logger,
        private PostIndexer $postIndexer,
        private PostRepository $postRepository,
    ){}

    public function handle(PostSaved $message): void
    {
        $post = $this->postRepository->find($message->getPostId());

        try {
            if ($post->isPublished()) {
                $this->postIndexer->index($post);
            } else {
                $this->postIndexer->remove($post);
            }
        } catch (Exception $e) {
            $this->logger->error('Error occurred while indexing post.', compact('e'));
        }
    }

    public static function getHandledMessages(): iterable
    {
        yield PostSaved::class => ['method' => 'handle', 'priority' => 0];
    }
}