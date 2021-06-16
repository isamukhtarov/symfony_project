<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Command\Category;

use Ria\Bundle\PostBundle\Entity\Category\Category;

class UpdateCategoryCommand extends CategoryCommand
{
    private Category $category;

    public function __construct(Category $category, array $locales, string $currentLocale)
    {
        $this->category = $category;
        $this->currentLocale = $currentLocale;
        $this->status = $category->getStatus();
        $this->parent = $category->getParent();
        $this->template = (string) $category->getTemplate();

        foreach ($locales as $locale)
            $this->translations[$locale] = new CategoryTranslationCommand($locale, $category->getTranslation($locale));
    }

    public function getCategory(): Category
    {
        return $this->category;
    }
}