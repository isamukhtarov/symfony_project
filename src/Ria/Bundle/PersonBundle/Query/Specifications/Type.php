<?php
declare(strict_types=1);

namespace Ria\Bundle\PersonBundle\Query\Specifications;


use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;

/**
 * Class Type
 * @package Ria\Persons\Core\Query\Specifications
 */
class Type extends BaseSpecification
{
    /**
     * @var string
     */
    private $type;

    /**
     * Type constructor.
     * @param string $type
     * @param null $dqlAlias
     */
    public function __construct(string $type, $dqlAlias = null)
    {
        $this->type = $type;
        parent::__construct($dqlAlias);
    }

    /**
     * @return \Happyr\DoctrineSpecification\Filter\Comparison
     * |\Happyr\DoctrineSpecification\Filter\Filter
     * |\Happyr\DoctrineSpecification\Query\QueryModifier
     * |null
     */
    public function getSpec()
    {
        return Spec::eq('type.type', $this->type);
    }
}