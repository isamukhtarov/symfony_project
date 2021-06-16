<?php

namespace Ria\Bundle\PostBundle\Query\Specifications\Post;

use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Query\Select as QuerySelect;

class Select extends BaseSpecification
{

    /**
     * @var array
     */
    private $fields;

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
        return new QuerySelect($this->fields ?: $this->getDefaultFields());
    }

    /**
     * @return array
     */
    private function getDefaultFields(): array
    {
        return [
            'id', 'title', 'slug', 'publishedAt', 'type.type', 'description', 'language'
        ];
    }

}