<?php

namespace Ria\Bundle\PostBundle\Query\Specifications\Post;


use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;

/**
 * Class Story
 * @package Ria\News\Core\Query\Specifications\Post
 */
class Story extends BaseSpecification
{
    /**
     * @var string|array
     */
    private $storyId;

    /**
     * Story constructor.
     * @param $storyId
     * @param null $dqlAlias
     */
    public function __construct($storyId, $dqlAlias = null)
    {
        $this->storyId = is_array($storyId) ? $storyId : [$storyId];
        parent::__construct($dqlAlias);
    }

    /**
     * @return \Happyr\DoctrineSpecification\Filter\Filter|\Happyr\DoctrineSpecification\Filter\In|\Happyr\DoctrineSpecification\Query\QueryModifier|null
     */
    public function getSpec()
    {
        return Spec::in('story', $this->storyId);
    }

}