<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Messenger\Subscriber;

use Exception;
use Doctrine\ORM\EntityManagerInterface;
use Ria\Bundle\PostBundle\Entity\Post\Post;
use Ria\Bundle\PostBundle\Entity\Post\ShortLink;
use Ria\Bundle\PostBundle\Repository\PostRepository;
use Ria\Bundle\PostBundle\Messenger\Message\PostSaved;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class CreateShortLinkSubscriber implements MessageSubscriberInterface
{
    public function __construct(
        private PostRepository $postRepository,
        private EntityManagerInterface $entityManager,
        private UrlGeneratorInterface $urlGenerator,
    ){}

    public function handle(PostSaved $message): void
    {
        $post = $this->postRepository->find($message->getPostId());

        if ($post !== null && $post->isPublished())
            $this->syncShortLink($post);
    }

    public static function getHandledMessages(): iterable
    {
        yield PostSaved::class => [
            'method'   => 'handle',
            'priority' => 10,
        ];
    }

    private function syncShortLink(Post $post): void
    {
        $shortLink = $this->getShortLink($post->getId());

        if ($shortLink === null) {
            $shortLink = (new ShortLink())
                ->setPost($post)
                ->setHash($this->generateHash());
        }

        $postUrl = $this->getPostAbsoluteUrl($post);

        if ($postUrl !== $shortLink->getRedirectTo()) {
            $shortLink->setRedirectTo($postUrl);

            $this->entityManager->persist($shortLink);
            $this->entityManager->flush();
        }
    }

    private function getShortLink(int $postId): ?ShortLink
    {
        $repository = $this->entityManager->getRepository(ShortLink::class);
        return $repository->findOneBy(['post' => $postId]);
    }

    private function getPostAbsoluteUrl(Post $post): string
    {
        return $this->urlGenerator->generate('post_view', [
            'categorySlug' => $post->getCategory()->getTranslation($post->getLanguage())->getSlug(),
            'slug' => $post->getSlug(),
            '_locale' => $post->getLanguage(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    private function generateHash(): string
    {
        try {
            $hash = substr(bin2hex(random_bytes(32)), 0, 5);
        } catch (Exception) {
            $hash = substr(uniqid(true), 0, 5);
        } finally {
            return $hash;
        }
    }
}