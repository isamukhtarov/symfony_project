<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Form\Grid\Column;

use Twig\Environment;
use Ria\Bundle\UserBundle\Entity\User;
use Ria\Bundle\PostBundle\Enum\PostIsBusy;
use Symfony\Component\Security\Core\Security;
use Ria\Bundle\PostBundle\Repository\PostRepository;
use SymfonyBundles\RedisBundle\Redis\ClientInterface as RedisClient;

class PostTranslationColumn
{
    public function __construct(
        private PostRepository $postRepository,
        private Environment $twig,
        private RedisClient $redisClient,
        private Security $security,
    ) {}

    public function render(int $postId): string
    {
        $post = $this->postRepository->find($postId);

        return $this->twig->render('@RiaPost/posts/grid/translation_column.html.twig', [
            'post'   => $post,
            'column' => $this,
            'postParentId' => $post->getParent()->getId(),
        ]);
    }

    public function isBusy(int $postParentId, string $language): bool
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $cacheKey = PostIsBusy::getCacheTranslationKey($postParentId, $language);
        $cacheData = $this->redisClient->hgetall($cacheKey);

        return count($cacheData) > 0 && $cacheData['id'] != $user->getId();
    }
}