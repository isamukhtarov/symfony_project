<?php

namespace Ria\Bundle\PostBundle\Query\Specifications\Post;


use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;

/**
 * Class Latest
 * @package Ria\News\Core\Query\Specifications\Post
 */
class Latest extends BaseSpecification
{

    /**
     * @return \Happyr\DoctrineSpecification\Query\OrderBy
     * |\Happyr\DoctrineSpecification\Query\OrderBy
     * |\Happyr\DoctrineSpecification\Query\QueryModifier
     * |null
     */
    public function getSpec()
    {
        return Spec::orderBy('publishedAt', 'DESC');
    }

}