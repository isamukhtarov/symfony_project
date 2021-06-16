<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Pipe;

use Ria\Bundle\CoreBundle\Component\HtmlBuilder;
use Ria\Bundle\PostBundle\Entity\Widget\Type;
use Closure;
use Ria\Bundle\PostBundle\Query\ViewModel\WidgetViewModel;

/**
 * Class ExtractAmpTwitterWidgets
 * @package Ria\Bundle\PostBundle\Pipe
 */
class ExtractAmpTwitterWidgets extends ExtractWidgets
{

    public function handle(string $content, Closure $next): string
    {
        $twitterWidgets = $this->getWidgets($content)->where('type', Type::TWITTER);

        /** @var WidgetViewModel $widget */
        foreach ($twitterWidgets as $widget) {

            if (!preg_match('/https:\/\/twitter.com\/[a-z0-9\-\_]+\/status\/(?P<twitter_id>[0-9]+)/ui', $widget->content, $matches)) {
                continue;
            }

            $search = sprintf("{{widget-content-%s}}", $widget->id);
            $replace = $this->getAmpTwitter((int)$matches['twitter_id']);

            $content = str_replace($search, $replace, $content);
        }

        return $next($content);
    }

    protected function getAmpTwitter(int $twitterId) : string
    {
        $attributes = HtmlBuilder::tagAttributes([
            'data-tweetid' => $twitterId,
            'layout'       => 'responsive',
            'width'        => 375,
            'height'       => 472
        ]);

        return "<amp-twitter {$attributes}></amp-twitter>";
    }
}
