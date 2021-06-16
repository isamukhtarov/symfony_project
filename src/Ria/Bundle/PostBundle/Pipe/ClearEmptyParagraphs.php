<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Pipe;

use Closure;

/**
 * Class ClearEmptyParagraphs
 * @package Ria\Bundle\PostBundle\Pipe
 */
class ClearEmptyParagraphs
{

    public function handle(string $content, Closure $next): string
    {
        $content = preg_replace("/<p[^>]*>(\s|&nbsp;)*<\/p>/u", '', $content);;

        return $next($content);
    }

}