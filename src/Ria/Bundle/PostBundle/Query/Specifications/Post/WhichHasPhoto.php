<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Query\Specifications\Post;


use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;

/**
 * Class WhichHasPhoto
 * @package Ria\News\Core\Query\Specifications\Post
 */
class WhichHasPhoto extends BaseSpecification
{

    /**
     * @return \Happyr\DoctrineSpecification\Filter\Filter
     * |\Happyr\DoctrineSpecification\Filter\IsNotNull
     * |\Happyr\DoctrineSpecification\Query\QueryModifier|null
     */
    public function getSpec()
    {
        return Spec::isNotNull('image');
    }

}