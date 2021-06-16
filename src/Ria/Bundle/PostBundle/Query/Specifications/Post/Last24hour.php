<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Query\Specifications\Post;


use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;

/**
 * Class Last24hour
 * @package Ria\News\Core\Query\Specifications\Post
 */
class Last24hour extends BaseSpecification
{
    public function getSpec()
    {
        $yesterday = date('Y-m-d H:i:s', strtotime('-1 day', time()));

        return Spec::gt('publishedAt', $yesterday);

    }
}