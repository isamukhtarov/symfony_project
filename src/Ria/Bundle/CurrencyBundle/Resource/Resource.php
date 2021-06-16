<?php

declare(strict_types=1);

namespace Ria\Bundle\CurrencyBundle\Resource;

class Resource
{
    public function __construct(
        private string $code,
        private float $value,
        private int $nominal
    ){}

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return float
     */
    public function getValue(): float
    {
        return $this->value;
    }

    /**
     * @return int
     */
    public function getNominal(): int
    {
        return $this->nominal;
    }

}