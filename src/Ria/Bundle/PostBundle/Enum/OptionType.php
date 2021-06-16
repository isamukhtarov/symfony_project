<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Enum;

use Elao\Enum\Enum;
use JetBrains\PhpStorm\ArrayShape;

class OptionType extends Enum
{
    public const ADS = 'ads';
    public const CLOSED = 'closed';

    #[ArrayShape([self::ADS => "string", self::CLOSED => "string"])]
    public static function values(): array
    {
        return [
            self::ADS => 'Advertising news',
            self::CLOSED => 'Closed news',
        ];
    }
}