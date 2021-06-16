<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Messenger\Subscriber;

use Ria\Bundle\PostBundle\Repository\PostRepository;
use Ria\Bundle\CoreBundle\Component\Pipeline\PipelineInterface;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;
use Ria\Bundle\PostBundle\Messenger\Message\{PostCreated, PostUpdated};
use Ria\Bundle\PostBundle\Pipe\{GeneratePersonLink, HyperlinkReportWords};

class PostContentProcessorSubscriber implements MessageSubscriberInterface
{
    public function __construct(
        private PipelineInterface $pipeline,
        private PostRepository $postRepository,
    ){}

    public function handle(PostCreated|PostUpdated $message): void
    {
        $post = $this->postRepository->find($message->getPostId());
        $this->pipeline
            ->send($post)
            ->through([
                GeneratePersonLink::class,
                HyperlinkReportWords::class,
            ])
            ->thenReturn();
    }

    public static function getHandledMessages(): iterable
    {
        yield PostCreated::class => [
            'method'   => 'handle',
            'priority' => 95,
        ];

        yield PostUpdated::class => [
            'method'   => 'handle',
            'priority' => 95,
        ];
    }
}