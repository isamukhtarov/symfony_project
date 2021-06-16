<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Pipe;

use Closure;
use Ria\Bundle\PostBundle\Entity\Post\Post;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class HyperlinkReportWords
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
    ){}

    private const WORDS = [
        '“Report”' => [
            'strict' => false
        ],
        '"Report"' => [
            'strict' => false
        ],
        'Report'   => [
            'strict' => true
        ],
    ];

    public function handle(Post $post, Closure $next)
    {
        if (!$post->isPublished())
            return $next($post);

        $content = $post->getContent();
        foreach (self::WORDS as $word => $params) {
            $wordFound = $params['strict'] ? preg_match("/\b{$word}\b/", $content) : str_contains($content, $word);
            if (!$wordFound) continue;

            if (!str_contains($content, '>' . $word . '</a>')) {
                $pattern = $params['strict'] ? ('/\b' . $word . '\b/') : ('/' . $word . '/');
                $content = preg_replace(
                    $pattern,
                    "<a href=\"{$this->getPostLink($post)}\" target=\"_blank\">{$word}</a>",
                    $content,
                    1);
            }
            break;
        }

        $post->setContent($content);

        return $next($post);
    }

    protected function getPostLink(Post $post): string
    {
        $category = $post->getCategory()->getTranslation($post->getLanguage());
        return $this->urlGenerator->generate('post_view', [
            'categorySlug' => $category->getSlug(), 'slug' => $post->getSlug(), '_locale' => $post->getLanguage(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);
    }
}