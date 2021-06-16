<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Handler\Category;

use Ria\Bundle\CoreBundle\Entity\Meta;
use Ria\Bundle\PostBundle\Query\Repository\CategoryRepository;
use Ria\Bundle\PostBundle\Command\Category\UpdateCategoryCommand;

class UpdateCategoryHandler
{
    public function __construct(
        private CategoryRepository $categoryRepository,
    ){}

    public function handle(UpdateCategoryCommand $command): void
    {
        $category = $command->getCategory();
        $category->setStatus($command->status)->setParent($command->parent);

        foreach ($command->translations as $translationCommand) {
            $translation = $category->getTranslation($translationCommand->locale)
                ->setTitle($translationCommand->title)
                ->setSlug($translationCommand->slug)
                ->setLanguage($translationCommand->locale)
                ->setMeta(new Meta(...(array) $translationCommand->meta));

            $category->addTranslation($translation);
        }

        $this->categoryRepository->save($category);
    }
}