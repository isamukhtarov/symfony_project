<?php

declare(strict_types=1);

namespace Ria\Bundle\CoreBundle\Component\Sitemap;

/**
 * Class HtmlMapItem
 * @package core\services\sitemap
 * @property HtmlMapItem[] $children
 */
class HtmlMapItem
{
    public $label;

    public $url;

    public $children = [];

    /**
     * HtmlMapItem constructor.
     * @param $label
     * @param $url
     * @param array|callable|null $children
     */
    public function __construct($label, $url, $children = null)
    {
        $this->label = $label;
        $this->url = $url;
        if ($children) {
            $this->children = $children;
        }
    }

    /**
     * @param array|callable $children
     */
    public function setChildren($children)
    {
        $this->children = $children;
    }

    /**
     * @param HtmlMapItem|callable $child
     */
    public function addChild($child)
    {
        $this->children[] = $child;
    }
}