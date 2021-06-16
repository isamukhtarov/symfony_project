<?php

namespace Ria\Bundle\PostBundle\Query\Specifications\Post;


use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;

/**
 * Class Published
 * @package Ria\News\Core\Query\Specifications\Post
 */
class Published extends BaseSpecification
{

    /**
     * @return \Happyr\DoctrineSpecification\Filter\Comparison|\Happyr\DoctrineSpecification\Filter\Filter              |\Happyr\DoctrineSpecification\Query\QueryModifier|null
     */
    public function getSpec()
    {
        return Spec::eq('isPublished', 1);
    }

}