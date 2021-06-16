<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Messenger\Subscriber;

use Ria\Bundle\PostBundle\Dto\PlainPostDto;
use Ria\Bundle\PostBundle\Entity\Post\Post;
use Ria\Bundle\PostBundle\Repository\PostRepository;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;
use SymfonyBundles\RedisBundle\Redis\ClientInterface as RedisClient;
use Ria\Bundle\PostBundle\Messenger\Message\{PostCreated, PostUpdated};
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PostCacheFlushSubscriber implements MessageSubscriberInterface
{
    public function __construct(
        private RedisClient $redisClient,
        private PostRepository $postRepository,
        private ParameterBagInterface $parameterBag,
    ){}

    public function handle(PostCreated|PostUpdated $message): void
    {
        $post = $this->postRepository->find($message->getPostId());
        $oldPostDto = ($message instanceof PostUpdated) ? $message->getPlainPostDto() : null;
        $this->flushTranslatesCache($post, $oldPostDto);

        if ($post->isMain())
            $this->flushMainPageCache($post->getLanguage());

        $this->flushFeedPage($post->getLanguage());
        $this->flushLastNewsCache($post->getLanguage());
    }

    public static function getHandledMessages(): iterable
    {
        yield PostCreated::class => [
            'method' => 'handle',
            'priority' => 90,
        ];

        yield PostUpdated::class => [
            'method' => 'handle',
            'priority' => 85,
        ];
    }

    private function flushMainPageCache(string $language): void
    {
        foreach (['default', 'mobile'] as $theme)
            $this->redisClient->expire('cache_' . md5(sprintf('app/.%s.%s', $theme, $language)), 90);
    }

    private function flushFeedPage(string $language): void
    {
        // TODO: make this flexible.
        $feedPageUrls = [
            'ge' => '/son-xeberler/',
            'ru' => '/ru/posledniye-novosti/',
            'en' => '/en/latest-news/'
        ];

       $this->redisClient->expire('cache_' . md5(sprintf('app%s.default.%s', $feedPageUrls[$language], $language)), 90);
    }

    private function flushLastNewsCache(string $language): void
    {
        foreach (['default', 'mobile'] as $theme) {
            $langPrefix = $language == $this->parameterBag->get('app.locale') ? null : '/' . $language;
            $this->redisClient->expire( 'cache_' . md5(sprintf('app%s/news-feed/.%s.%s', $langPrefix, $theme, $language)), 90);
        }
    }

    private function flushTranslatesCache(Post $post, ?PlainPostDto $oldPostDto = null): void
    {
        $translations = array_merge(
            [$post], $this->postRepository->getOtherTranslations($post->getParent()->getId(), $post->getLanguage())
        );

        /** @var Post $translation */
        foreach ($translations as $i => $translation) {
            $langPrefix = ($translation->getLanguage() !== $this->parameterBag->get('app.locale'))
                ? ($translation->getLanguage() . '/')
                : null;

            if (!empty($oldPostDto) && ($oldPostDto->getLanguage() === $translation->getLanguage())) {
                $postSlug     = $oldPostDto->getSlug();
                $categorySlug = $oldPostDto->getCategorySlug();
            } else {
                $postSlug     = $translation->getSlug();
                $categorySlug = $translation->getCategory()->getTranslation($translation->getLanguage())->getSlug();
            }

            foreach (['default', 'mobile'] as $theme) {
                $this->redisClient->expire('cache_' . md5("app/{$langPrefix}{$categorySlug}/" . $postSlug . "/.{$theme}.{$translation->getLanguage()}"), 200);
                $this->redisClient->expire('cache_' . md5("app/{$langPrefix}amp/{$categorySlug}/" . $postSlug . "/.{$theme}.{$translation->getLanguage()}"), 200);

                if ($translation->isPublished())
                    $this->redisClient->expire('cache_' . md5('app/' . $categorySlug . '/.' . $theme . '.' . $translation->getLanguage()), 200);
            }
        }
    }
}