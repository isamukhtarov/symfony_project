<?php

namespace Ria\Bundle\PostBundle\Query\Specifications\Category;

use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;

class Latest extends BaseSpecification
{

    public function getSpec()
    {
        return Spec::orderBy('id', 'DESC');
    }

}