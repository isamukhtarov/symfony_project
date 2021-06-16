<?php
declare(strict_types=1);

namespace Ria\Persons\Core\Query\Specifications;

use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;

/**
 * Class Limit
 * @package Ria\Persons\Core\Query\Specifications
 */
class Limit extends BaseSpecification
{
    /**
     * @var int
     */
    private $limit;

    /**
     * Limit constructor.
     * @param int $limit
     * @param null $dqlAlias
     */
    public function __construct(int $limit, $dqlAlias = null)
    {
        $this->limit = $limit;
        parent::__construct($dqlAlias);
    }

    public function getSpec()
    {
        return Spec::limit($this->limit);
    }

}