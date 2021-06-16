<?php
declare(strict_types=1);

namespace Ria\Bundle\CoreBundle\Helper;

final class TokenHelper
{

    public static function clearAll(string $text): string
    {
        return self::clearExpertQuoteTokens(
            self::clearMediaWidgetTokens(
                self::clearPhotoTokens(
                    self::clearVoteTokens($text)
                )
            )
        );
    }

    public static function clearVoteTokens(string $content): string
    {
        return preg_replace('/({{vote-[\d]+}})/i', '', $content);
    }

    public static function clearExpertQuoteTokens(string $content): string
    {
        return preg_replace('/({{expert-quote-[\d]+}})/i', '', $content);
    }

    public static function clearMediaWidgetTokens(string $content): string
    {
        return preg_replace('/({{widget-content-[\d]+}})/i', '', $content);
    }

    public static function clearPhotoTokens(string $content): string
    {
        return preg_replace('/({{photo-(small-left|small-right|big)-[\d]+}})/i', '', $content);
    }

}