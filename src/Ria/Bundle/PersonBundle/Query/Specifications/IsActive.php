<?php
declare(strict_types=1);

namespace Ria\Bundle\PersonBundle\Query\Specifications;


use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;

/**
 * Class IsActive
 * @package Ria\Persons\Core\Query\Specifications
 */
class IsActive extends BaseSpecification
{

    public function getSpec()
    {
        return Spec::eq('status', true);
    }

}