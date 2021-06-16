<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Enum;

use Elao\Enum\Enum;
use Elao\Enum\AutoDiscoveredValuesTrait;

final class PostIsBusy extends Enum
{
    use AutoDiscoveredValuesTrait;

    public const POST_UPDATE_KEY = 'post-is-busy';
    public const POST_TRANSLATION_CREATE_KEY = 'post-translation-is-busy';

    public static function getCacheUpdateKey(int|string $uniqId): string
    {
        return self::POST_UPDATE_KEY . '.' . $uniqId;
    }

    public static function getCacheTranslationKey(int $postParentId, string $language): string
    {
        return self::POST_TRANSLATION_CREATE_KEY . '.' . $postParentId . '.' . $language;
    }
}