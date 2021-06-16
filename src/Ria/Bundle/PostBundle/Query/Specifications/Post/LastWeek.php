<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Query\Specifications\Post;


use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;

/**
 * Class LastWeek
 * @package Ria\News\Core\Query\Specifications\Post
 */
class LastWeek extends BaseSpecification
{
    /**
     * @return \Happyr\DoctrineSpecification\Filter\Comparison
     * |\Happyr\DoctrineSpecification\Filter\Filter
     * |\Happyr\DoctrineSpecification\Query\QueryModifier|null
     */
    public function getSpec()
    {
        $lastWeek = date('Y-m-d H:i:s', strtotime('-1 week', time()));

        return Spec::gt('publishedAt', $lastWeek);
    }
}