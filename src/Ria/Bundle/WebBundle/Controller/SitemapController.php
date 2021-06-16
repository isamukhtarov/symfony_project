<?php

declare(strict_types=1);

namespace Ria\Bundle\WebBundle\Controller;

use Doctrine\ORM\AbstractQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Ria\Bundle\CoreBundle\Component\Sitemap\MapItem;
use Ria\Bundle\PostBundle\Repository\PostRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use SymfonyBundles\RedisBundle\Redis\ClientInterface as RedisClient;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Class SitemapController
 * @package Ria\Bundle\WebBundle\Controller
 */
class SitemapController extends AbstractController
{
    const ITEMS_PER_PAGE = 10000;

    public function __construct(
        private ParameterBagInterface $parameterBag,
        private UrlGeneratorInterface $urlGenerator,
        private PostRepository $postRepository,
        protected RedisClient $redisClient,
    ) {}

    #[Route("/sitemap", name: "sitemap", methods: ['GET'], priority: 2)]
    public function index(Request $request): Response
    {
        $language = $request->getLocale();
        return $this->renderSitemap($this->redisClient->get("sitemap-{$language}.xml"));
    }

    #[Route("/sitemap/posts/{start<\d+>}", name: "sitemap_posts", methods: ['GET'], priority: 2)]
    public function posts(Request $request, int $start = 0): Response
    {
        ini_set('memory_limit', '512M');
        return $this->renderSitemap($this->generatePosts($start, $request->getLocale()));
    }

    public function generatePosts(int $page, string $language): string
    {
        $this->redisClient->del("sitemap-posts-{$language}-{$page}.xml");
        if (!$this->redisClient->exists("sitemap-posts-{$language}-{$page}.xml")) {
            $languageForUrl = ($language == $this->parameterBag->get('app.locale')) ? null : $language;
            $offset         = $page * self::ITEMS_PER_PAGE;
            $posts          = $this->postRepository
                ->getByRange((int)$offset, self::ITEMS_PER_PAGE, $language, AbstractQuery::HYDRATE_ARRAY);

            $postItems = array_map(function (array $post) use ($languageForUrl) {
                return new MapItem(
                    $this->urlGenerator->generate('post_view', [
                            'categorySlug' => $post['category_slug'],
                            'slug' => $post['slug'],
                            '_locale' => $languageForUrl
                        ], UrlGeneratorInterface::ABSOLUTE_URL),
                    $post['updatedAt']->getTimestamp()
                );
            }, $posts);

            $res = $this->renderView("@RiaWeb/sitemap/partials/posts.html.twig", compact("postItems"));

            $this->redisClient->set("sitemap-posts-{$language}-{$page}.xml", $res);
        }

        return $this->redisClient->get("sitemap-posts-{$language}-{$page}.xml");
    }

    #[Route("/sitemap/categories", name: "sitemap_categories", methods: ['GET'], priority: 2)]
    public function categories(Request $request): Response
    {
        $language = $request->getLocale();
        return $this->renderSitemap($this->redisClient->get("sitemap-categories-{$language}.xml"));
    }

    public function renderSitemap($content): Response
    {
        $response = new Response($content);
        $response->headers->set('Content-Type', 'application/xml');

        return $response;
    }

}