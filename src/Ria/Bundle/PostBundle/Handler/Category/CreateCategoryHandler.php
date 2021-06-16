<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Handler\Category;

use Ria\Bundle\CoreBundle\Entity\Meta;
use Ria\Bundle\PostBundle\Entity\Category\Category;
use Ria\Bundle\PostBundle\Entity\Category\Template;
use Ria\Bundle\PostBundle\Query\Repository\CategoryRepository;
use Ria\Bundle\PostBundle\Command\Category\CreateCategoryCommand;
use Ria\Bundle\PostBundle\Entity\Category\Translation as CategoryTranslation;

class CreateCategoryHandler
{
    public function __construct(
        private CategoryRepository $categoryRepository,
    ){}

    public function handle(CreateCategoryCommand $command): void
    {
        $category = new Category();
        $category->setStatus($command->status)
            ->setSort($this->categoryRepository->getLastOrder() + 1)
            ->setTemplate(new Template($command->template))
            ->setParent($command->parent);

        foreach ($command->translations as $translationCommand) {
            $category->addTranslation((new CategoryTranslation())
                ->setTitle($translationCommand->title)
                ->setSlug($translationCommand->slug)
                ->setLanguage($translationCommand->locale)
                ->setMeta(new Meta(...(array) $translationCommand->meta)));
        }

        $this->categoryRepository->save($category);
    }
}