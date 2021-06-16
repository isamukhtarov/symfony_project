<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Query\Specifications\Story;

use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;

class Latest extends BaseSpecification
{

    public function getSpec()
    {
        return Spec::orderBy('id', 'DESC');
    }

}