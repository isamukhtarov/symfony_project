<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Command\Region;

use JetBrains\PhpStorm\Pure;

class CreateRegionCommand extends AbstractRegionCommand
{
    #[Pure] public function __construct(array $locales)
    {
        foreach ($locales as $locale)
            $this->translations[$locale] = new CreateRegionTranslationCommand($locale);
    }
}