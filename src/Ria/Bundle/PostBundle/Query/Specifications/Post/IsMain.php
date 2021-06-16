<?php

namespace Ria\Bundle\PostBundle\Query\Specifications\Post;


use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;

class IsMain extends BaseSpecification
{

    public function getSpec()
    {
        return Spec::eq('isMain', true);
    }

}