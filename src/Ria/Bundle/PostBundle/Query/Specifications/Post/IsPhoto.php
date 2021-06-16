<?php

namespace Ria\Bundle\PostBundle\Query\Specifications\Post;


use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;

class IsPhoto extends BaseSpecification
{
    public function getSpec()
    {
        return Spec::eq('type.type', 'photo');
    }
}