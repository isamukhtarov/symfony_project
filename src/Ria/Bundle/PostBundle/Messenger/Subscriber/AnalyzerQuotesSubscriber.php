<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Messenger\Subscriber;

use Ria\Bundle\PostBundle\Repository\PostRepository;
use Ria\Bundle\PostBundle\Messenger\Message\PostCreated;
use Ria\Bundle\PostBundle\Repository\ExpertQuoteRepository;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class AnalyzerQuotesSubscriber implements MessageSubscriberInterface
{
    public function __construct(
        private PostRepository $postRepository,
        private ExpertQuoteRepository $expertQuoteRepository,
    ){}

    public function handleCreatedPost(PostCreated $message): void
    {
        $post = $this->postRepository->find($message->getPostId());

        preg_match_all('/{{expert-quote-(\d+)}}/i', (string) $post->getContent(), $matches);

        foreach ($matches[1] as $quoteId) {
            if (($quote = $this->expertQuoteRepository->find((int) $quoteId)) !== null) {
                $quote->setPost($post);
                $this->expertQuoteRepository->save($quote);
            }
        }
    }

    public static function getHandledMessages(): iterable
    {
        yield PostCreated::class => [
            'method'   => 'handleCreatedPost',
            'priority' => 85,
        ];
    }
}