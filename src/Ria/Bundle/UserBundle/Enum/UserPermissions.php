<?php

namespace Ria\Bundle\UserBundle\Enum;

use Elao\Enum\Enum;
use Elao\Enum\AutoDiscoveredValuesTrait;

final class UserPermissions extends Enum
{
    use AutoDiscoveredValuesTrait;

    public const MANAGE = 'manageUsers';
    public const VIEW = 'viewUsers';
    public const CAN_BE_AUTHOR = 'canBeAuthor';
}