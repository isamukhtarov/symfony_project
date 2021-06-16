<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Form\Grid\Column;

use Ria\Bundle\PostBundle\Enum\PostIsBusy;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use SymfonyBundles\RedisBundle\Redis\ClientInterface as RedisClient;
use Twig\Environment;

class PostIsBusyColumn
{
    public function __construct(
        private RedisClient $redisClient,
        private Environment $twig,
        private ParameterBagInterface $parameterBag,
    ) {}

    public function render(int $postId, int $postParentId): ?string
    {
        $resultHtml = '';

        $updateCacheKey = PostIsBusy::getCacheUpdateKey($postId);

        if ($this->redisClient->exists($updateCacheKey)) {
            $cacheData = $this->redisClient->hgetall($updateCacheKey);

            $resultHtml .= $this->twig->render('@RiaPost/posts/grid/is_busy_column.html.twig', [
                'postId' => $postId,
                'bussierName' => $cacheData['name'],
            ]);
        }

        foreach ($this->parameterBag->get('app.supported_locales') as $locale) {
            $translationCreateCacheKey = PostIsBusy::getCacheTranslationKey($postParentId, $locale);

            if ($this->redisClient->exists($translationCreateCacheKey)) {
                $cacheData = $this->redisClient->hgetall($translationCreateCacheKey);

                $resultHtml .= $this->twig->render('@RiaPost/posts/grid/is_busy_translation_column.html.twig', [
                    'bussierName' => $cacheData['name'],
                    'postParentId' => $postParentId,
                    'language' => $locale
                ]);
            }
        }

        return $resultHtml;
    }

}