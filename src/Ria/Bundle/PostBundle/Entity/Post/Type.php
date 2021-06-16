<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Entity\Post;

use InvalidArgumentException;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable()
 */
class Type
{
    public const POST        = 'post';
    public const PHOTO       = 'photo';
    public const VIDEO       = 'video';
    public const ARTICLE     = 'article';
    public const INFOGRAPHIC = 'infographic';
    public const OPINION     = 'opinion';
    public const PARTNERS    = 'partners';

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $type;

    public function __construct(string $type)
    {
        if (!in_array($type, self::all()))
            throw new InvalidArgumentException('Invalid post type: ' . $type);
        $this->type = $type;
    }

    public static function all(): array
    {
        return [
            self::POST,
            self::ARTICLE,
            self::VIDEO,
            self::PHOTO,
            self::INFOGRAPHIC,
            self::OPINION,
            self::PARTNERS
        ];
    }

    public static function media(): array
    {
        return [self::VIDEO, self::PHOTO, self::INFOGRAPHIC];
    }

    public function isVideo(): bool
    {
        return $this->type === self::VIDEO;
    }

    public function isPhoto(): bool
    {
        return $this->type === self::PHOTO;
    }

    public function toValue(): string
    {
        return $this->type;
    }
}
