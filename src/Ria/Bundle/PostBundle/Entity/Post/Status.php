<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Entity\Post;

use InvalidArgumentException;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

/**
 * @ORM\Embeddable()
 */
class Status
{
    public const CREATED                = 'created';
    public const ON_MODERATION          = 'on_moderation';
    public const WAITING_FOR_CORRECTION = 'waiting_for_correction';
    public const READ                   = 'read';
    public const DELETED                = 'deleted';
    public const ARCHIVED               = 'archived';
    public const PRIVATE                = 'private';

    /**
     * @ORM\Column(type="string", length=50)
     */
    private string $status;

    public function __construct(string $status)
    {
        if (!in_array($status, self::all()))
            throw new InvalidArgumentException('Invalid post status: ' . $status);
        $this->status = $status;
    }

    public static function all(): array
    {
        return [
            self::READ,
            self::CREATED,
            self::ON_MODERATION,
            self::WAITING_FOR_CORRECTION,
            self::PRIVATE,
            self::ARCHIVED,
            self::DELETED
        ];
    }

    public static function list(): array
    {
        return [
            self::CREATED,
            self::ON_MODERATION,
            self::WAITING_FOR_CORRECTION,
            self::READ,
            self::PRIVATE,
            self::ARCHIVED
        ];
    }

    public static function activeOnes(): array
    {
        return [
            self::CREATED,
            self::ON_MODERATION,
            self::WAITING_FOR_CORRECTION,
            self::READ,
            self::PRIVATE
        ];
    }

    public static function publishedOnes(): array
    {
        return [self::READ];
    }

    #[Pure] public function isActive(): bool
    {
        return in_array($this->status, self::publishedOnes());
    }

    public function is(string $status): bool
    {
        return $this->status === $status;
    }

    public function isPrivate(): bool
    {
        return $this->status === self::PRIVATE;
    }

    public function isDeleted(): bool
    {
        return $this->status === self::DELETED;
    }

    public function isModerated(): bool
    {
        return $this->status === self::ON_MODERATION;
    }

    public function toValue(): string
    {
        return $this->status;
    }
}