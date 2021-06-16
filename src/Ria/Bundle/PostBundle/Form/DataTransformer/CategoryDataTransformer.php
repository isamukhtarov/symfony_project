<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Form\DataTransformer;

use Ria\Bundle\PostBundle\Query\Repository\CategoryRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CategoryDataTransformer
{
    public function __construct(
        private ParameterBagInterface $parameterBag,
        private CategoryRepository $categoryRepository,
    ){}

    public function transform(array $params = []): array
    {
        $language = $params['language'] ?? $this->parameterBag->get('app.locale');

        $tree = [];
        foreach ($this->categoryRepository->getAll($language) as $category) {
            $label = $category->getTranslation($language)->getTitle();
            $children = $category->getChildren();
            if ($children->isEmpty()) {
                $tree[$label] = $category->getId();
                continue;
            }

            foreach ($children as $child)
                $tree[$label][$child->getTranslation($language)->getTitle()] = $child->getId();
        }

        return $tree;
    }
}