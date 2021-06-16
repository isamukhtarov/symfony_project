<?php
declare(strict_types=1);

namespace Ria\Bundle\WebBundle\Controller;

use Ria\Bundle\CoreBundle\Component\CommandValidator\CommandValidatorInterface;
use Ria\Bundle\CoreBundle\Component\CommandValidator\TimestampCommand;
use Ria\Bundle\PostBundle\Entity\Category\Category;
use Ria\Bundle\PostBundle\Entity\Category\Translation;
use Ria\Bundle\PostBundle\Helper\CategoryHelper;
use Ria\Bundle\PostBundle\Query\Repository\CategoryRepository;
use Ria\Bundle\PostBundle\Query\ViewModel\CategoryViewModel;
use Ria\Bundle\PostBundle\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class PlaylistController
 * @package Ria\Bundle\WebBUndle\Controller
 */
class PlaylistController extends AbstractController
{

    private const POSTS_COUNT = 9;

    private const TYPE_ALL  = 'all';
    private const TYPE_MAIN = 'main';

    public function __construct(
        protected PostRepository $postRepository,
        protected CategoryRepository $categoryRepository
    )
    {
    }

    #[Route("/playlist/{categorySlug}/", name: "playlist_with_category", methods: ['GET'], priority: 2)]
    #[Route("/playlist/{type<main>}/", name: "playlist_with_type", methods: ['GET'], priority: 2)]
    #[Route("/playlist/{type<main>}/{categorySlug}/", name: "playlist_with_type_and_category", methods: ['GET'], priority: 2)]
    #[Route("/playlist/", name: "playlist", methods: ['GET'], priority: 2)]
    public function index(Request $request, ?string $type = null, ?string $categorySlug = null): Response
    {
        if ($categorySlug) {
            $category = $this->categoryRepository->getBySlug($categorySlug, $request->getLocale());
            if (!$category) {
                throw $this->createNotFoundException();
            }
        }

        $categories = $this->categoryRepository->menuItems($request->getLocale(), 15);
        $posts      = $this->postRepository->getLastPosts(
            $request->getLocale(),
            self::POSTS_COUNT,
            null,
            $this->getAdditionalSpecs($type, $category ?? null)
        );

        return $this->render('@RiaWeb/index/playlist.html.twig', [
            'categories'      => $categories,
            'posts'           => $posts,
            'urlTranslations' => $this->getUrlTranslations($type, $category->id ?? null),
            'type'            => empty($type) ? self::TYPE_ALL : self::TYPE_MAIN,
            'categorySlug'    => $category->slug ?? null
        ]);
    }

    #[Route("/playlist-ajax/", name: "playlist_ajax", methods: ['GET'], priority: 2)]
    public function playlistAjax(Request $request, CommandValidatorInterface $validator): JsonResponse
    {
        $timestamp = $request->get('timestamp');
        if (!$validator->validate(new TimestampCommand(), source: $request)) {
            throw $this->createNotFoundException();
        }

        $type = $request->get('type');
        if (!in_array($type, [self::TYPE_MAIN, self::TYPE_ALL])) {
            throw $this->createNotFoundException();
        }

        $categorySlug = $request->get('categorySlug');
        if ($categorySlug) {
            $category = $this->categoryRepository->getBySlug($categorySlug, $request->getLocale());
            if (!$category) {
                throw $this->createNotFoundException();
            }
        }

        $posts = $this->postRepository->getLastPosts(
            $request->getLocale(),
            self::POSTS_COUNT,
            null,
            $this->getAdditionalSpecs($request->get('type'), $category ?? null, $timestamp),
        );

        return new JsonResponse([
            'html'         => $this->renderView('@RiaWeb/index/partials/_audio-post-item.html.twig', compact('posts')),
            'limitReached' => count($posts) < self::POSTS_COUNT
        ]);
    }

    private function getAdditionalSpecs(?string $type, ?CategoryViewModel $category, string $timestamp = null): array
    {
        $additionalSpecs = [[
            'withSpeech' => ['language' => $this->get('request_stack')->getCurrentRequest()->getLocale()]
        ]];

        if ($type == self::TYPE_MAIN) {
            $additionalSpecs[] = 'isMain';
        }

        if ($category) {
            $additionalSpecs[] = ['category' => CategoryHelper::pluckChildren(
                $category->id,
                $this->categoryRepository->getSubCategories($category->id, $category->language)
            )];
        }

        if ($timestamp) {
            $additionalSpecs[] = ['dateLess' => $timestamp];
        }

        return $additionalSpecs;
    }

    protected function getUrlTranslations(?string $type, ?int $categoryId): array
    {
        $urls = [];

        if ($categoryId) {
            /** @var Category $category */
            $category = $this->categoryRepository->find($categoryId);

            if ($type) {
                $route  = 'playlist_with_type_and_category';
                $params = ['type' => $type];
            } else {
                $route  = 'playlist_with_category';
                $params = [];
            }

            /** @var Translation $translation */
            foreach ($this->getParameter('app.supported_locales') as $language) {
                $translation = $category->getTranslation($language);

                $urls[$translation->getLanguage()] = $this->generateUrl(
                    $route,
                    array_merge($params, ['categorySlug' => $translation->getSlug(), '_locale' => $translation->getLanguage()]),
                    UrlGeneratorInterface::ABSOLUTE_URL);
            }
        } else {
            if ($type) {
                $route  = 'playlist_with_type';
                $params = ['type' => $type];
            } else {
                $route  = 'playlist';
                $params = [];
            }

            foreach ($this->getParameter('app.supported_locales') as $language) {
                $urls[$language] = $this->generateUrl(
                    $route,
                    array_merge($params, ['_locale' => $language]),
                    UrlGeneratorInterface::ABSOLUTE_URL);
            }
        }

        return $urls;
    }

}