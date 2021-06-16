<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Pipe;

use Closure;
use Doctrine\ORM\EntityManagerInterface;
use Ria\Bundle\CoreBundle\Component\HtmlBuilder;
use Ria\Bundle\PostBundle\Entity\Widget\Type;
use Ria\Bundle\PostBundle\Query\ViewModel\WidgetViewModel;

/**
 * Class ExtractInstagramWidgets
 * @package Ria\Bundle\PostBundle\Pipe
 */
class ExtractInstagramWidgets extends ExtractWidgets
{

    protected string $replaceFormat;

    public function __construct(protected EntityManagerInterface $entityManager, $replaceFormat = 'iframe')
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

    public function handle(string $content, Closure $next) : string
    {
        if ($this->replaceFormat == 'iframe') {
            return $next($content);
        }

        $instagramWidgets = $this->getWidgets($content)->where('type', Type::INSTAGRAM);

        foreach ($instagramWidgets as $widget) {
            /** @var WidgetViewModel $widget */

            if (!preg_match('/https:\/\/www.instagram.com\/(p|tv)\/(?P<instagram_hash>[a-z0-9\-\_]+)\//ui', $widget->content, $matches)) {
                continue;
            }

            $search = sprintf("{{widget-content-%s}}", $widget->id);
            $replace = $this->getAmpInstagram($matches['instagram_hash']);

            $content = str_replace($search, $replace, $content);
        }

        return $next($content);
    }

    private function getAmpInstagram(string $instagramCode) : string
    {
        $attributes = HtmlBuilder::tagAttributes([
            'data-shortcode' => $instagramCode,
            'data-captioned' => 'data-captioned',
            'layout'         => 'responsive',
            'width'          => 400,
            'height'         => 400
        ]);

        return "<amp-instagram {$attributes}></amp-instagram>";
    }

}