<?php

namespace Ria\Bundle\PostBundle\Query\Specifications\Post;


use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;

/**
 * Class DateFrom
 * @package Ria\News\Core\Query\Specifications\Post
 */
class DateFrom extends BaseSpecification
{

    /**
     * @var string
     */
    public $datetime;

    /**
     * Limit constructor.
     * @param string $datetime
     * @param null $dqlAlias
     */
    public function __construct(string $datetime, $dqlAlias = null)
    {
        $this->datetime = $datetime;
        parent::__construct($dqlAlias);
    }

    /**
     * @return \Happyr\DoctrineSpecification\Filter\Comparison
     * |\Happyr\DoctrineSpecification\Filter\Filter
     * |\Happyr\DoctrineSpecification\Query\QueryModifier
     * |null
     */
    public function getSpec()
    {
        return Spec::gt('published_at', $this->datetime);
    }

}