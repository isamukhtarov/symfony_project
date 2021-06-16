<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Command\City;

use JetBrains\PhpStorm\Pure;

class CreateCityCommand extends AbstractCityCommand
{
    #[Pure] public function __construct(array $locales, string $currentLocale)
    {
        foreach ($locales as $locale)
            $this->translations[$locale] = new CreateCityTranslationCommand($locale);
        $this->currentLocale = $currentLocale;
    }
}