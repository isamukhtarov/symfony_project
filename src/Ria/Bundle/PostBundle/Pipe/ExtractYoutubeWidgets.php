<?php

namespace Ria\Bundle\PostBundle\Pipe;

use Closure;
use Doctrine\ORM\EntityManagerInterface;
use Ria\Bundle\CoreBundle\Component\HtmlBuilder;
use Ria\Bundle\PostBundle\Entity\Widget\Type;
use Ria\Bundle\PostBundle\Query\ViewModel\WidgetViewModel;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Class ExtractYoutubeWidgets
 * @package Ria\Bundle\PostBundle\Pipe
 */
class ExtractYoutubeWidgets extends ExtractWidgets
{

    protected string $replaceFormat;

    public function __construct(
        protected EntityManagerInterface $entityManager,
        private ParameterBagInterface $parameterBag,
        $replaceFormat = 'iframe',
    )
    {
        parent::__construct($this->entityManager);

        $this->setReplaceFormat($replaceFormat);
    }

    public function setReplaceFormat(string $format): void
    {
        if (!in_array($format, ['iframe', 'amp'])) {
            throw new \InvalidArgumentException('Invalid format given');
        }
        $this->replaceFormat = $format;
    }

    public function handle(string $content, Closure $next): string
    {
        $youtubeWidgets = $this->getWidgets($content)
            ->where('type', Type::YOUTUBE);

        foreach ($youtubeWidgets as $widget) {
            /** @var WidgetViewModel $widget */

            if (!preg_match('/\/embed\/(?P<youtube_hash>[a-z0-9\_\-]+)/i', $widget->content, $matches)) {
                continue;
            }

            $search  = sprintf("{{widget-content-%s}}", $widget->id);
            $replace = $this->{'get' . ucfirst($this->replaceFormat)}($matches['youtube_hash']);

            $content = str_replace($search, $replace, $content);
        }

        return $next($content);
    }

    private function getIframe(string $hash, string $width = '100%'): string
    {
        $iframeAttributes = HtmlBuilder::tagAttributes([
            'type'            => 'text/html',
            'width'           => $width,
            'height'          => '468',
            'src'             => 'https://www.youtube.com/embed/' . $hash . '?rel=0&color=white&hl=' . $this->parameterBag->get('app.locale'),
            'frameborder'     => 0,
            'allowfullscreen' => 'allowfullscreen'
        ]);

        $iframe = "<iframe {$iframeAttributes}></iframe>";

        return "<div class='video-container'>{$iframe}</div>";
    }

    private function getAmp(string $youtubeId): string
    {
        $attributes = HtmlBuilder::tagAttributes([
            'data-videoid' => $youtubeId,
            'layout'       => 'responsive',
            'width'        => 480,
            'height'       => 270
        ]);

        return "<amp-youtube {$attributes}></amp-youtube>";
    }

}