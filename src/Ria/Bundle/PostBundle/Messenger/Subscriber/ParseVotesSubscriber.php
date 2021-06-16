<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Messenger\Subscriber;

use Doctrine\ORM\EntityManagerInterface;
use Ria\Bundle\PostBundle\Messenger\Message\PostSaved;
use Ria\Bundle\PostBundle\Repository\PostRepository;
use Ria\Bundle\VoteBundle\Entity\Vote;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class ParseVotesSubscriber implements MessageSubscriberInterface
{
    public function __construct(
        private PostRepository $postRepository,
        private EntityManagerInterface $entityManager,
    ) {}

    public function handleSavedPost(PostSaved $message): void
    {
        $post = $this->postRepository->find($message->getPostId());

        preg_match_all('/{{vote-(?P<voteId>[\d]+)}}/i', (string) $post->getContent(), $matches);

        foreach ($matches['voteId'] as $i => $voteId) {
            $vote = $this->entityManager->getReference(Vote::class, $voteId);

            if (!$post->getVotes()->contains($vote)) {
                $post->getVotes()->add($vote);
                $this->postRepository->save($post);
            }
        }
    }

    public static function getHandledMessages(): iterable
    {
        yield PostSaved::class => [
            'method'   => 'handleSavedPost',
            'priority' => 85,
        ];
    }

}