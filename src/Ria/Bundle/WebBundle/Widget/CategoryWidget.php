<?php
declare(strict_types=1);

namespace Ria\Bundle\WebBundle\Widget;

use Ria\Bundle\CoreBundle\Web\FrontendWidget;
use Ria\Bundle\PostBundle\Query\Repository\CategoryRepository;
use Ria\Bundle\PostBundle\Repository\PostRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

/**
 * Class CategoryWidget
 * @package Ria\Bundle\WebBundle\Widget
 *
 * @property-read int categoriesCount
 * @property-read int categoryPostsCount
 */
class CategoryWidget extends FrontendWidget
{

    protected ContainerInterface $container;
    protected CategoryRepository $categoryRepository;
    protected PostRepository $postRepository;

    public function __construct(
        Environment $twig,
        ContainerInterface $container,
        CategoryRepository $categoryRepository,
        PostRepository $postRepository
    )
    {
        $this->container          = $container;
        $this->categoryRepository = $categoryRepository;
        $this->postRepository     = $postRepository;

        parent::__construct($twig);
    }

    public function run(): string
    {
        /** @var Request $request */
        $request = $this->container->get('request_stack')->getCurrentRequest();

        $categories = $this->getCategories($request->getLocale());

        return $this->render($this->template, compact('categories'));
    }

    protected function getCategories(string $language): array
    {
        $parentCategories = $this->categoryRepository->menuItems($language, $this->categoriesCount);

        foreach ($parentCategories as $i => &$category) {
            $children           = $this->categoryRepository->getChildren($category->id);
            $categoryIds        = is_array($category->id) ? $category->id : [$category->id];
            $categoriesToSearch = array_merge($categoryIds, array_column($children, 'id'));

            $posts = $this->postRepository->match(
                [
                    'select',
                    ['category' => $categoriesToSearch],
                    ['lang'     => $language],
                    ['limit'    => $this->categoryPostsCount],
                    'withCategory',
                    'withPhoto',
                    'published',
                    'latest'
                ]
            );

            if (!count($posts)) {
                unset($parentCategories[$i]);
                continue;
            }

            $category->setPosts($posts);
        }

        return $parentCategories;
    }
}