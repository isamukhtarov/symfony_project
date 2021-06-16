<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Pipe;

use Closure;

/**
 * Class RemoveStyleAttributes
 * @package Ria\Bundle\PostBundle\Pipe
 */
class RemoveStyleAttributes
{

    public function handle(string $content, Closure $next): string
    {
        $patterns = [
            '/(<[^>]+) style=".*?"/i',
            "/(<[^>]+) style='.*?'/i"
        ];
        foreach ($patterns as $pattern) {
            $content = preg_replace($pattern, '$1', $content);
        }
        return $next($content);
    }
}