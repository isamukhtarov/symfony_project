<?php

namespace Ria\Bundle\PostBundle\Query\Specifications\Post;


use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;

class IsArticle extends BaseSpecification
{

    public function getSpec()
    {
        return Spec::eq('type', 'article');
    }

}