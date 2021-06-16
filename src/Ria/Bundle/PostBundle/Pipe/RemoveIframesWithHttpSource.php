<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Pipe;

use Closure;

/**
 * Class RemoveIframesWithHttpSource
 * @package Ria\News\Core\Pipes
 */
class RemoveIframesWithHttpSource
{

    public function handle(string $content, Closure $next): string
    {
        $content =  preg_replace('/(<iframe.*?src="http:\/\/.*".*>.*[\n\r]?<\/iframe>)/', '', $content);

        return $next($content);
    }
}