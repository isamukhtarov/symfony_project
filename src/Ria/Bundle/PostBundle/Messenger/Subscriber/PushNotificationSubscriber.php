<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Messenger\Subscriber;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Ria\Bundle\PostBundle\Repository\PostRepository;
use Ria\Bundle\PostBundle\Messenger\Message\PostSaved;
use Ria\Bundle\PostBundle\Service\PushNotificationService;
use Ria\Bundle\PostBundle\Entity\Post\{Post, Export, Notification};
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class PushNotificationSubscriber // implements MessageSubscriberInterface
{
    public function __construct(
        private PostRepository $postRepository,
        private EntityManagerInterface $entityManager,
        private PushNotificationService $pushNotificationService,
    ){}

    public function handle(PostSaved $message): void
    {
        $post = $this->postRepository->find($message->getPostId());

        if (!$post->isPublished()
            || !$this->hasPushNotificationExport($post)
            || !$this->readyForNotification($post)) {
            return;
        }

        if ($post->getNotification() === null) {
            $isSent = $this->sendPushNotification($post);
            $notification = new Notification();
            $notification->setStatus($isSent);
            $notification->setPost($post);
            $notification->setCreatedAt(new DateTime());

            $this->entityManager->persist($notification);
            $this->entityManager->flush();
        }
    }

    public static function getHandledMessages(): iterable
    {
        yield PostSaved::class => [
            'method' => 'handle',
            'priority' => 9,
        ];
    }

    public function sendPushNotification(Post $post): bool
    {
        $data = [
            'id'            => $post->getId(),
            'language'      => $post->getLanguage(),
            'status'        => $post->getStatus(),
            'slug'          => $post->getSlug(),
            'category_slug' => $post->getCategory()->getTranslation($post->getLanguage())->getSlug(),
            'title'         => $post->getTitle(),
            'description'   => $post->getDescription(),
            'photo'         => $post->getPhoto(),
        ];

        return $this->pushNotificationService->setData($data)->notify();
    }

    private function hasPushNotificationExport(Post $post): bool
    {
        foreach ($post->getExports() as $export)
            if ($export->toValue() === Export::PUSH_NOTIFICATIONS)
                return true;
        return false;
    }

    private function readyForNotification(Post $post): bool
    {
        return (new DateTime())->diff($post->getPublishedAt())->format('G') <= 12;
    }
}