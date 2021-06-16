<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Enum;

use Elao\Enum\AutoDiscoveredValuesTrait;
use Elao\Enum\Enum;

final class PostPreview extends Enum
{
    use AutoDiscoveredValuesTrait;

    private const CACHE_KEY = 'post.preview.';

    public static function getCacheKey(string $uniqKey): string
    {
        return self::CACHE_KEY . $uniqKey;
    }
}