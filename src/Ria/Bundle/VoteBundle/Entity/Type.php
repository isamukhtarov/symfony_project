<?php

declare(strict_types=1);

namespace Ria\Bundle\VoteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Type
 * @package Ria\Bundle\VoteBundle\Entity
 * @ORM\Embeddable()
 */
class Type
{
    public const TYPE_SIMPLE = 'simple';
    public const TYPE_PRIZE = 'prize';

    /**
     * @ORM\Column(type="string", length=8, options={"default"="simple"})
     */
    private string $type;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public static function all(): array
    {
        return [self::TYPE_SIMPLE, self::TYPE_PRIZE];
    }

    public function isPrize(): bool
    {
        return $this->type == self::TYPE_PRIZE;
    }

    public function toValue(): string
    {
        return $this->type;
    }
}