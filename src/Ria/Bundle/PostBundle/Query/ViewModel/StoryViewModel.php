<?php

namespace Ria\Bundle\PostBundle\Query\ViewModel;

use Ria\Bundle\CoreBundle\Query\ViewModel;

/**
 * Class StoryViewModel
 * @package Ria\Bundle\PostBundle\Query\ViewModel
 *
 * @property-read int $id
 * @property-read string $title
 * @property-read string $description
 * @property-read string $slug
 * @property-read string|null $cover
 * @property-read string $language
 * @property-read array $posts
 */
class StoryViewModel extends ViewModel
{

    /**
     * @return string
     */
    public function getTitleWithoutQuotes(): string
    {
        return str_replace('"', '', $this->title);
    }

    /**
     * @param array $posts
     */
    public function setPosts(array $posts)
    {
        $this->posts = $posts;
    }

}