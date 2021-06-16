<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Query\Specifications\Story;

use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;

class Limit extends BaseSpecification
{

    private int $limit;

    public function __construct(int $limit, ?string $dqlAlias = null)
    {
        $this->limit = $limit;
        parent::__construct($dqlAlias);
    }

    public function getSpec(): \Happyr\DoctrineSpecification\Query\Limit
    {
        return Spec::limit($this->limit);
    }

}