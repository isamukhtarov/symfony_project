<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Handler\Category;

use Ria\Bundle\PostBundle\Query\Repository\CategoryRepository;
use Ria\Bundle\PostBundle\Command\Category\DeleteCategoryCommand;

class DeleteCategoryHandler
{
    public function __construct(
        private CategoryRepository $categoryRepository,
    ){}

    public function handle(DeleteCategoryCommand $command): void
    {
        if (($category = $this->categoryRepository->find($command->getId())) === null) return;
        $this->categoryRepository->remove($category);
    }
}
