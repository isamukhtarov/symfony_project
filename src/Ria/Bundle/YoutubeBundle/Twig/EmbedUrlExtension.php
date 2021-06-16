<?php

declare(strict_types=1);

namespace Ria\Bundle\YoutubeBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class EmbedUrlExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('embedUrl', [$this, 'getEmbedUrl'], ['is_safe' => ['all']])
        ];
    }

    public function getEmbedUrl(string $youtubeId): string
    {
        return empty($youtubeId) ? '' : 'https://www.youtube.com/embed/' . $youtubeId;
    }
}