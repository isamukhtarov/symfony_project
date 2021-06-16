<?php
declare(strict_types=1);

namespace Ria\Bundle\WebBundle\Widget;

use Twig\Environment;
use Ria\Bundle\CoreBundle\Web\FrontendWidget;
use Symfony\Component\HttpFoundation\RequestStack;
use Ria\Bundle\PostBundle\Query\Repository\CategoryRepository;
use SymfonyBundles\RedisBundle\Redis\ClientInterface as RedisClient;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


class MenuWidget extends FrontendWidget
{
    protected array $cacheKeys = [];

    public function __construct(
        private RequestStack $requestStack,
        private CategoryRepository $categoryRepository,
        protected Environment $twig,
        protected RedisClient $redisClient,
        protected ParameterBagInterface $parameterBag,
    ) {
        parent::__construct($this->twig);

        $this->cacheKeys = $parameterBag->get('app.cache_keys');
    }

    public function run(): string
    {
        $language = $this->requestStack->getCurrentRequest()->getLocale();

        if (!$this->redisClient->exists("{$this->cacheKeys['menu']}-{$language}")) {
            $this->redisClient->set(
                "{$this->cacheKeys['menu']}-{$language}",
                serialize($this->categoryRepository->menuItems($language, 8))
            );
        }

        return $this->render($this->template, [
            'items' => unserialize($this->redisClient->get("{$this->cacheKeys['menu']}-{$language}"))
        ]);
    }

}