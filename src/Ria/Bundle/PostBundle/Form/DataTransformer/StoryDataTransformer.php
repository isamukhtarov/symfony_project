<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Form\DataTransformer;

use Ria\Bundle\PostBundle\Query\Repository\StoryRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class StoryDataTransformer
{
    public function __construct(
        private StoryRepository $storyRepository,
        private ParameterBagInterface $parameterBag,
    ){}

    public function transform(array $params = []): array
    {
        $language = $params['language'] ?? $this->parameterBag->get('app.locale');
        $list = [];
        foreach ($this->storyRepository->getAll($language) as $story)
            $list[$story->getTranslation($language)->getTitle()] = $story->getId();
        return $list;
    }
}