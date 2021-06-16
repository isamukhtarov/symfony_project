<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Pipe;

use Closure;
use Ria\Bundle\CoreBundle\Component\HtmlBuilder;
use Ria\Bundle\PostBundle\Entity\Widget\Type;
use Ria\Bundle\PostBundle\Query\ViewModel\WidgetViewModel;

/**
 * Class ExtractAmpVkWidgets
 * @package Ria\Bundle\PostBundle\Pipe
 */
class ExtractAmpVkWidgets extends ExtractWidgets
{

    public function handle(string $content, Closure $next) : string
    {
        $vkWidgets = $this->getWidgets($content)
            ->where('type', Type::TWITTER);

        foreach ($vkWidgets as $widget) {
            /** @var WidgetViewModel $widget */

            if (!preg_match('/VK\.Widgets\.Post\("vk_post_(?P<ownerId>[0-9\-]+)_(?P<postId>[0-9]+)", [0-9\-]+, [0-9]+, \'(?P<hash>[a-z0-9]+)\'\)/i', $widget->content, $matches)) {
                continue;
            }

            $search = sprintf("{{widget-content-%s}}", $widget->id);
            $params = [$matches['ownerId'], $matches['postId'], $matches['hash']];
            $replace = $this->getAmpVk($params);

            $content = str_replace($search, $replace, $content);
        }

        return $next($content);
    }

    protected function getAmpVk(array $params) : string
    {
        $attributes = HtmlBuilder::tagAttributes([
            'data-embedtype' => 'post',
            'data-owner-id'  => $params['ownerId'],
            'data-post-id'   => $params['postId'],
            'data-hash'      => $params['hash'],
            'layout'         => 'responsive',
            'width'          => 500,
            'height'         => 300
        ]);

        return "<amp-vk {$attributes}></amp-vk>";
    }
}