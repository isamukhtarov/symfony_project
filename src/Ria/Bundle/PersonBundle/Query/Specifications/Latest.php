<?php
declare(strict_types=1);

namespace Ria\Persons\Core\Query\Specifications;

use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;

/**
 * Class Latest
 * @package Ria\Persons\Core\Query\Specifications
 */
class Latest extends BaseSpecification
{

    public function getSpec()
    {
        return Spec::orderBy('id', 'DESC');
    }

}