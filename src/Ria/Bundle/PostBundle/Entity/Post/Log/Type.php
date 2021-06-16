<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Entity\Post\Log;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Type
{
    public const TYPE_CREATED            = 'created';
    public const TYPE_VIEWED             = 'viewed';
    public const TYPE_CORRECTED          = 'corrected';
    public const TYPE_UPDATED            = 'updated';
    public const TYPE_DELETED            = 'deleted';
    public const TYPE_ARCHIVED           = 'archived';
    public const TYPE_SENT_TO_MODERATION = 'sent_to_moderation';

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private string $type;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public static function all(): array
    {
        return [
            self::TYPE_CREATED,
            self::TYPE_VIEWED,
            self::TYPE_CORRECTED,
            self::TYPE_UPDATED,
            self::TYPE_DELETED,
            self::TYPE_ARCHIVED,
            self::TYPE_SENT_TO_MODERATION,
        ];
    }

    public function is(string $type): bool
    {
        return $this->type === $type;
    }

    public function toValue(): string
    {
        return $this->type;
    }
}