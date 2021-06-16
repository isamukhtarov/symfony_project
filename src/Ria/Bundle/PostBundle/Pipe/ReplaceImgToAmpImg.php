<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Pipe;

use Closure;

/**
 * Class ExtractVote
 * @package Ria\Bundle\PostBundle\Pipe
 */
class ReplaceImgToAmpImg
{

    private array $options;

    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    public function handle(string $content, Closure $next): string
    {
        $content = $this->replaceImgToAmpImg($content);

        return $next($content);
    }

    private function replaceImgToAmpImg(string $html) : string
    {
        $html = preg_replace(
            '/(<img[^>]+>(?:<\/img>)?)/i',
            '$1</amp-img>',
            $html
        );
        $attributes = str_replace("=", '="',
            http_build_query($this->options, '', '" ', PHP_QUERY_RFC3986));
        return str_replace('<img', '<amp-img ' . $attributes, $html);
    }
}