<?php

namespace Ria\Bundle\PostBundle\Query\Specifications\Post;


use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;

/**
 * Class Exclude
 * @package Ria\News\Core\Query\Specifications\Post
 */
class Exclude extends BaseSpecification
{
    /**
     * @var string|array
     */
    private $id;

    /**
     * Limit constructor.
     * @param string|array $id
     * @param null $dqlAlias
     */
    public function __construct($id, $dqlAlias = null)
    {
        $this->id = $id;
        parent::__construct($dqlAlias);
    }

    /**
     * @return \Happyr\DoctrineSpecification\Filter\Comparison|\Happyr\DoctrineSpecification\Filter\Filter|\Happyr\DoctrineSpecification\Filter\In|\Happyr\DoctrineSpecification\Query\QueryModifier|null
     */
    public function getSpec()
    {
        return Spec::notIn('id', array_wrap($this->id));
    }

}