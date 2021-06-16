<?php
declare(strict_types=1);

namespace Ria\Bundle\WebBundle\Controller;

use Ria\Bundle\CoreBundle\Component\CommandValidator\CommandValidatorInterface;
use Ria\Bundle\CoreBundle\Component\CommandValidator\TimestampCommand;
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
 * Class CategoryController
 * @package Ria\Bundle\WebBundle\Controller
 */
class CategoryController extends AbstractController
{

    protected const POSTS_COUNT = 20;

    public function __construct(
        protected CategoryRepository $categoryRepository,
        protected PostRepository $postRepository,
    )
    {
    }

    #[Route("/{slug}/", name: "category_view", methods: ['GET'], priority: 0)]
    #[Route("/{slug}/{page<\d+>}/", name: "category_with_page", methods: ['GET'], priority: 0)]
    public function view(Request $request, string $slug, ?int $page = null): Response
    {
        $category      = $this->getCategory($slug, $request->getLocale());
        $subCategories = $this->getSubCategories($category);

        $category->setPosts($this->postRepository->getByCategories(
            $category->language,
            CategoryHelper::pluckChildren($category->id, $subCategories),
            self::POSTS_COUNT,
            $page ? [['offset' => ($page - 1) * self::POSTS_COUNT]] : []
        ));

        $urlTranslations = $this->getUrlTranslations($category->id, $page);

        return $this->render('@RiaWeb/index/category.html.twig',
            compact('category', 'subCategories', 'urlTranslations'));
    }

    #[Route("/category-ajax/{categoryId<\d+>}/", name: "category_ajax", methods: ['GET'], priority: 2)]
    public function categoryAjax(Request $request, CommandValidatorInterface $validator, int $categoryId): JsonResponse
    {
        $timestamp = $request->get('timestamp');
        if (!$validator->validate(new TimestampCommand(), source: $request)) {
            throw $this->createNotFoundException();
        }

        $category      = $this->categoryRepository->getById($categoryId, $request->getLocale());
        $subCategories = $this->getSubCategories($category);

        $posts = $this->postRepository->getByCategories(
            $category->language,
            CategoryHelper::pluckChildren($category->id, $subCategories),
            self::POSTS_COUNT,
            [['dateLess' => $timestamp]]
        );

        return new JsonResponse([
            'html' => $this->renderView('@RiaWeb/index/partials/_category-post-item.html.twig', compact('posts')),
            'limitReached' => count($posts) < self::POSTS_COUNT
        ]);
    }

    protected function getCategory(string $slug, string $language): CategoryViewModel
    {
        $category = $this->categoryRepository->getBySlug($slug, $language);
        if (!$category) {
            throw $this->createNotFoundException();
        }
        return $category;
    }

    /**
     * @param CategoryViewModel $category
     * @return CategoryViewModel[]
     */
    protected function getSubCategories(CategoryViewModel $category): array
    {
        return empty($category->parent_id)
            ? $this->categoryRepository->getSubCategories($category->id, $category->language)
            : [];
    }

    protected function getUrlTranslations(int $categoryId, ?int $page): array
    {
        $model = $this->categoryRepository->find($categoryId);

        $params = [];
        if ($page) {
            $params += ['page' => $page];
        }

        $urls = [];
        foreach ($this->getParameter('app.supported_locales') as $language) {
            $translation     = $model->getTranslation($language);
            $params += ['slug' => $translation->getSlug(), '_locale' => $translation->getLanguage()];

            $urls[$language] = $this->generateUrl(
                $page ? 'category_with_page' : 'category_view',
                $params,
                UrlGeneratorInterface::ABSOLUTE_URL);
        }

        return $urls;
    }

}