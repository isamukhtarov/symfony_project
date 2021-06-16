<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Enum;

use Elao\Enum\Enum;
use Elao\Enum\AutoDiscoveredValuesTrait;

final class CategoryPermissions extends Enum
{
    use AutoDiscoveredValuesTrait;

    public const MANAGE = 'manageCategories';
}