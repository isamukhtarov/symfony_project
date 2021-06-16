<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Entity\Post;

use InvalidArgumentException;

class Icon
{
    const LIGHTNING = 'lightning';
    const PHOTO = 'camera';
    const VIDEO = 'video-camera';
    const INFOGRAPHICS = 'bar-chart';

    private string|null $icon;

    public function __construct(string|null $icon = null)
    {
        $this->set($icon);
    }

    public function set(string|null $icon): void
    {
        if ($icon && !in_array($icon, self::all()))
            throw new InvalidArgumentException('Invalid post icon: ' . $icon);

        $this->icon = $icon;
    }

    public static function all(): array
    {
        return [
            self::LIGHTNING,
            self::VIDEO,
            self::PHOTO,
            self::INFOGRAPHICS,
        ];
    }

    public function getIcon(): string
    {
        return $this->icon ?? '';
    }
}