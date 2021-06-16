<?php

namespace Ria\Bundle\PostBundle\Pipe;

use Closure;
use Doctrine\ORM\EntityManagerInterface;
use Ria\Bundle\CoreBundle\Component\HtmlBuilder;
use Ria\Bundle\PostBundle\Entity\Widget\Type;
use Ria\Bundle\PostBundle\Query\ViewModel\WidgetViewModel;

/**
 * Class ExtractFacebookWidgets
 * @package Ria\News\Core\Pipes
 */
class ExtractFacebookWidgets extends ExtractWidgets
{

    private string $replaceFormat;

    public function __construct(protected EntityManagerInterface $entityManager, $replaceFormat = 'div')
    {
        parent::__construct($this->entityManager);

        $this->setReplaceFormat($replaceFormat);
    }

    public function setReplaceFormat(string $format): void
    {
        if (!in_array($format, ['div', 'amp'])) {
            throw new \InvalidArgumentException('Invalid format given');
        }
        $this->replaceFormat = $format;
    }

    public function handle(string $content, Closure $next): string
    {
        $facebookWidgets = $this->getWidgets($content)
            ->where('type', Type::FACEBOOK);

        /** @var WidgetViewModel $widget */
        foreach ($facebookWidgets as $widget) {
            $content = str_replace(
                sprintf("{{widget-content-%s}}", $widget->id),
                $this->{'get' . ucfirst($this->replaceFormat)}($widget->content),
                $content
            );
        }

        return $next($content);
    }

    private function getAmp(string $facebookLink): string
    {
        $attributes = HtmlBuilder::tagAttributes([
            'data-href' => $facebookLink,
            'layout'    => 'responsive',
            'width'     => 552,
            'height'    => 310
        ]);

        return "<amp-facebook {$attributes}></amp-facebook>";
    }

    private function getDiv(string $iframe): string
    {
        return "<div class=\"facebook-container\">{$iframe}</div>";
    }

}