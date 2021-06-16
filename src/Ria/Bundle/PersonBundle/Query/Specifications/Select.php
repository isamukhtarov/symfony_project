<?php
declare(strict_types=1);

namespace Ria\Bundle\PersonBundle\Query\Specifications;

use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Query\Select as QuerySelect;

/**
 * Class Select
 * @package Ria\Persons\Core\Query\Specifications
 */
class Select extends BaseSpecification
{
    /**
     * @var array
     */
    private array $default_fields = [
        'id'
    ];

    /**
     * @var array
     */
    private array $fields;

    /**
     * Select constructor.
     * @param array $fields
     * @param null $dqlAlias
     */
    public function __construct(array $fields = [], $dqlAlias = null)
    {
        parent::__construct($dqlAlias);
        $this->fields = $fields;
    }

    public function getSpec()
    {
        return new QuerySelect($this->fields ?: $this->default_fields);
    }

}