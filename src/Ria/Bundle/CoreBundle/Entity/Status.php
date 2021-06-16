<?php

declare(strict_types=1);

namespace Ria\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable()
 */
class Status
{
    private const ACTIVE   = 1;
    private const INACTIVE = 0;

    /**
     * @ORM\Column(type="integer", options={"default":0})
     */
    private int $status;

    public function __construct(int $status)
    {
        $this->status = $status;
    }

    public function isActive(): bool
    {
        return $this->status === self::ACTIVE;
    }

    public function isInactive(): bool
    {
        return $this->status === self::INACTIVE;
    }

    public function toValue(): int
    {
        return $this->status;
    }
}
