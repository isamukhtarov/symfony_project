<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Command\Category;

use JetBrains\PhpStorm\Pure;

class CreateCategoryCommand extends CategoryCommand
{
    #[Pure] public function __construct(array $locales, string $currentLocale)
    {
        foreach ($locales as $locale)
            $this->translations[$locale] = new CreateCategoryTranslationCommand($locale);
        $this->currentLocale = $currentLocale;
    }
}