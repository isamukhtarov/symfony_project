<?php

namespace Ria\Bundle\PostBundle\Query\Specifications\Post;


use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;

class IsOpinionAndArticle extends BaseSpecification
{
    public function getSpec()
    {
        return Spec::orX(Spec::eq('type', 'opinion'), Spec::eq('type', 'article'));
    }
}