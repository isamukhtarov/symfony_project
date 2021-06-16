<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Query\Specifications\Post;


use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;

/**
 * Class IsImportant
 * @package Ria\News\Core\Query\Specifications\Post
 */
class IsImportant extends BaseSpecification
{

    /**
     * @return \Happyr\DoctrineSpecification\Filter\Comparison
     * |\Happyr\DoctrineSpecification\Filter\Filter
     * |\Happyr\DoctrineSpecification\Query\QueryModifier|null
     */
    public function getSpec()
    {
        return Spec::eq('isImportant', true);
    }

}