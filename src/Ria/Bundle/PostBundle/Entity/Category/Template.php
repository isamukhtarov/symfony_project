<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Entity\Category;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable()
 */
class Template
{
    const DEFAULT      = 'default';
    const ECONOMICS    = 'economics';
    const SPORTS       = 'sports';
    const FOOTBALL     = 'football';
    const TECHNOLOGIES = 'technologies';

    /**
     * @ORM\Column(type="string")
     */
    public string $template;

    public function __construct(string $template)
    {
        $this->template = $template;
    }

    public static function all(): array
    {
        return [
            self::DEFAULT,
            self::ECONOMICS,
            self::SPORTS,
            self::FOOTBALL,
            self::TECHNOLOGIES
        ];
    }

    public function __toString(): string
    {
        return $this->template;
    }
}