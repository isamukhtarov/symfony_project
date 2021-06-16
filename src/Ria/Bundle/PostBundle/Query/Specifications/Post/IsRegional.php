<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Query\Specifications\Post;


use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;

class IsRegional extends BaseSpecification
{

    public function getSpec()
    {
        return Spec::gt('city', 0);
    }

}