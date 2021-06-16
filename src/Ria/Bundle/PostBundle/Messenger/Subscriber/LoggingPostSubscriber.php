<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Messenger\Subscriber;

use DateTime;
use Symfony\Component\DomCrawler\Crawler;
use Twig\Environment;
use Caxy\HtmlDiff\HtmlDiff;
use Ria\Bundle\UserBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Ria\Bundle\PostBundle\Entity\Post\Post;
use Ria\Bundle\PostBundle\Dto\PlainPostDto;
use Ria\Bundle\PostBundle\Entity\Post\Status;
use Ria\Bundle\PostBundle\Entity\Post\Log\{Log, Type};
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;
use Ria\Bundle\PostBundle\Messenger\Message\{PostCanceled, PostCreated, PostDeleted, PostUpdated};

class LoggingPostSubscriber implements MessageSubscriberInterface
{
    private User $user;
    private Post $post;

    public function __construct(
        private Environment $twig,
        private EntityManagerInterface $entityManager,
    ){}

    public function handleCreatedPost(PostCreated $message)
    {
        $this->setUserAndPost($message);
        $this->createLog(Type::TYPE_CREATED, $this->takeSnapshot(Type::TYPE_CREATED));
    }

    public function handleUpdatedPost(PostUpdated $message)
    {
        $this->setUserAndPost($message);
        $old = $message->getPlainPostDto();
        $new = PlainPostDto::fromEntity($this->post);

        if ($old == $new) { // Nothing was changed.
            $this->createLog(Type::TYPE_VIEWED);
            return;
        }

        $type = match (true) {
            $this->user->hasRole('corrector') => Type::TYPE_CORRECTED,
            ($this->post->getStatus()->isModerated() && $old->getStatus() !== Status::ON_MODERATION) => Type::TYPE_SENT_TO_MODERATION,
            default => Type::TYPE_UPDATED,
        };

        $this->createLog($type, $this->takeSnapshot($type, $old));
    }

    public function handleDeletedPost(PostDeleted $message)
    {
        $this->setUserAndPost($message);
        $this->createLog(Type::TYPE_DELETED);
    }

    private function takeSnapshot(string $type, ?PlainPostDto $old = null): string
    {
        $newHtml = $this->twig->render('@RiaPost/posts/snapshot.html.twig', [
            'post' => PlainPostDto::fromEntity($this->post),
            'isNewRecord' => ($type === Type::TYPE_CREATED),
        ]);

        if (!$old) return $newHtml;

        $oldHtml = $this->twig->render('@RiaPost/posts/snapshot.html.twig', [
            'post' => $old,
            'isNewRecord' => false,
        ]);

        return $this->getSnapshotWithOnlyChangedData(
            (new HtmlDiff($oldHtml, $newHtml))->build()
        );
    }

    public function handleCanceledPost(PostCanceled $message)
    {
        $this->setUserAndPost($message);
        $this->createLog(Type::TYPE_VIEWED);
    }

    private function createLog(string $type, ?string $snapshot = null)
    {
        $log = (new Log())
            ->setType(new Type($type))
            ->setPost($this->post)
            ->setUser($this->user)
            ->setSnapshot($snapshot)
            ->setCreatedAt(new DateTime());
        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }

    private function setUserAndPost(object $message): void
    {
        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->find($message->getUserId());
        /** @var Post $post */
        $post = $this->entityManager->getRepository(Post::class)->find($message->getPostId());

        $this->user = $user;
        $this->post = $post;
    }

    private function getSnapshotWithOnlyChangedData(string $html): string
    {
        $crawler = new Crawler($html);
        $crawler->filter('table tr')->each(function (Crawler $node) {
            if ($node->filter('td del,td ins')->count() === 0) {
                $child = $node->getNode(0);
                $child->parentNode->removeChild($child);
            }
        });
        return $crawler->filter('body > table')->outerHtml();
    }

    public static function getHandledMessages(): iterable
    {
        yield PostCreated::class => [
            'method'   => 'handleCreatedPost',
            'priority' => 0,
        ];

        yield PostUpdated::class => [
            'method' => 'handleUpdatedPost',
            'priority' => 0,
        ];

        yield PostDeleted::class => [
            'method'   => 'handleDeletedPost',
            'priority' => 0
        ];

        yield PostCanceled::class => [
            'method' =>  'handleCanceledPost',
            'priority' => 0
        ];
    }
}