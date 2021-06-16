<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Query\Specifications\Story;

use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Query\Select as QuerySelect;

/**
 * Class Select
 * @package Ria\Bundle\PostBundle\Query\Specifications\Story
 */
class Select extends BaseSpecification
{

    private array $default_fields = [
        'id', 'status'
    ];

    private array $fields;

    public function __construct(array $fields = [], ?string $dqlAlias = null)
    {
        parent::__construct($dqlAlias);
        $this->fields = $fields;
    }

    public function getSpec(): QuerySelect
    {
        return new QuerySelect($this->fields ?: $this->default_fields);
    }

}