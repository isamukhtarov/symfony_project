<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Pipe;

use Closure;

/**
 * Class StylizeSimpleBlockquotes
 * @package Ria\Bundle\PostBundle\Pipe
 */
class StylizeSimpleBlockquotes
{

    public function handle(string $content, Closure $next): string
    {
        $content = str_replace('<blockquote>', '<blockquote class="blockquote">', $content);

        return $next($content);
    }

}