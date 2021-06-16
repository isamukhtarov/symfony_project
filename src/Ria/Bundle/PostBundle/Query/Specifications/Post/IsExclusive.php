<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Query\Specifications\Post;


use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;

/**
 * Class IsExclusive
 * @package Ria\News\Core\Query\Specifications\Post
 */
class IsExclusive extends BaseSpecification
{

    public function getSpec()
    {
        return Spec::eq('isExclusive', true);
    }

}