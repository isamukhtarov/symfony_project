<?php
declare(strict_types=1);

namespace Ria\Bundle\CoreBundle\Query;

use Doctrine\ORM\QueryBuilder;
use Happyr\DoctrineSpecification\Operand\Operand;
use Happyr\DoctrineSpecification\Query\Selection\Selection;

class SubSelect implements Operand, Selection
{

    /**
     * @var string
     */
    private $subSelect;

    /**
     * @var string|null
     */
    private $dqlAlias;

    /**
     * SubSelect constructor.
     * @param string $subSelect
     * @param null $dqlAlias
     */
    public function __construct(string $subSelect, $dqlAlias = null)
    {
        $this->subSelect = $subSelect;
        $this->dqlAlias = $dqlAlias;
    }

    /**
     * @param QueryBuilder $qb
     * @param string       $dqlAlias
     *
     * @return string
     */
    public function transform(QueryBuilder $qb, $dqlAlias)
    {
        return $this->subSelect;
    }

}