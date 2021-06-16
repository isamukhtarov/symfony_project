<?php

declare(strict_types=1);

namespace Ria\Bundle\UserBundle\Enum;

use Elao\Enum\Enum;
use Elao\Enum\AutoDiscoveredValuesTrait;

final class UserRoles extends Enum
{
    use AutoDiscoveredValuesTrait;

    public const TRANSLATOR = 'Translator';
    public const LEAD_TRANSLATOR = 'LeadTranslator';

    public static function getTranslatorRoles(): array
    {
        return [self::TRANSLATOR, self::LEAD_TRANSLATOR];
    }
}