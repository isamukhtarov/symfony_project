<?php

namespace Ria\Bundle\PostBundle\Query\Specifications\Post;


use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;

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