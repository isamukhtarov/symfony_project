<?php

declare(strict_types=1);

namespace Ria\Bundle\ThemeBundle;

class ActiveTheme
{
    private string|null $currentTheme = null;

    public function __construct(
        private array $themes
    ){}

    public function getCurrentTheme(): string|null
    {
        return $this->currentTheme;
    }

    public function setCurrentTheme(string $theme): void
    {
        if (!in_array($theme, $this->getThemes())) {
            throw new \InvalidArgumentException(sprintf(
                'The active theme "%s" must be in the themes list (%s)',
                $theme, implode(',', $this->themes)
            ));
        }

        $this->currentTheme = $theme;
    }

    public function getThemes(): array
    {
        return $this->themes;
    }
}