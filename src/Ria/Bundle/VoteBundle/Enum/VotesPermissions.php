<?php

declare(strict_types=1);

namespace Ria\Bundle\VoteBundle\Enum;

use Elao\Enum\AutoDiscoveredValuesTrait;
use Elao\Enum\Enum;

final class VotesPermissions extends Enum
{
    use AutoDiscoveredValuesTrait;

    public const MANAGE = 'manageVotes';

}