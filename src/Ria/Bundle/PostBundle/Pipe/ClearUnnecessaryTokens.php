<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Pipe;

use Closure;
use Ria\Bundle\CoreBundle\Helper\TokenHelper;

/**
 * Class ClearUnnecessaryTokens
 * @package Ria\Bundle\PostBundle\Pipe
 */
class ClearUnnecessaryTokens
{

    public function handle(string $content, Closure $next): string
    {
        return $next(TokenHelper::clearMediaWidgetTokens($content));
    }

}