<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Pipe;

use Closure;

/**
 * Class ReplaceIframeToAmpIframe
 * @package Ria\Bundle\PostBundle\Pipe
 */
class ReplaceIframeToAmpIframe
{

    private array $options;

    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    public function handle(string $content, Closure $next): string
    {
        $content = $this->replaceIframeToAmpIframe($content);

        return $next($content);
    }

    private function replaceIframeToAmpIframe(string $html): string
    {
        $attributes = str_replace("=", '="',
            http_build_query($this->options, '', '" ', PHP_QUERY_RFC3986));

        return preg_replace(
            '/<iframe.*?src=["|\']([^"\']*)["|\'].*>.*[\n\r]?[<\/iframe>]?/',
            '<figure>
                        <amp-iframe src="$1" ' . $attributes . '>
                        </amp-iframe>
                    </figure>',
            $html
        );
    }
}
