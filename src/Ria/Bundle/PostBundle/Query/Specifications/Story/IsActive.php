<?php

namespace Ria\Bundle\PostBundle\Query\Specifications\Story;


use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;

class IsActive extends BaseSpecification
{

    public function getSpec()
    {
        return Spec::eq('status', true);
    }

}