<?php
declare(strict_types=1);

namespace Ria\Bundle\AdminBundle\Service;

use JetBrains\PhpStorm\Pure;

class FlagIconRenderer
{
    private const LOCALE_ICON = [
        'en' => 'us'
    ];

    #[Pure] public function render(string $locale): string
    {
        $iconLanguage = array_key_exists($locale, self::LOCALE_ICON)
            ? self::LOCALE_ICON[$locale]
            : $locale;

        return sprintf('<i class="flag-icon flag-icon-%s"></i>', $iconLanguage);
    }

}