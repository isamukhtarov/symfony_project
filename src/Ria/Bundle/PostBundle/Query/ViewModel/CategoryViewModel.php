<?php

namespace Ria\Bundle\PostBundle\Query\ViewModel;

use Ria\Bundle\CoreBundle\Entity\Meta;
use Ria\Bundle\CoreBundle\Query\ViewModel;

/**
 * Class CategoryViewModel
 * @package Ria\News\Core\Query\ViewModel
 *
 * @property int $id
 * @property int|null $parent_id
 * @property string $title
 * @property string $slug
 * @property Meta $meta
 * @property int|null $parent
 * @property-read string $language
 * @property-read array $posts
 *
 * @property-read string|null parent_title
 * @property-read string|null parent_slug
 */
class CategoryViewModel extends ViewModel
{
    /**
     * @param array $posts
     */
    public function setPosts(array $posts)
    {
        $this->posts = $posts;
    }
}