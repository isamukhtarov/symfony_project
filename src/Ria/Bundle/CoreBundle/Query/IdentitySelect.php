<?php
declare(strict_types=1);

namespace Ria\Bundle\CoreBundle\Query;

use Doctrine\ORM\QueryBuilder;
use Happyr\DoctrineSpecification\Operand\Operand;
use Happyr\DoctrineSpecification\Query\Selection\Selection;

class IdentitySelect implements Operand, Selection
{

    /**
     * @var string
     */
    private $fieldName;

    /**
     * @var string|null
     */
    private $dqlAlias;

    /**
     * @param string      $fieldName
     * @param string|null $dqlAlias
     */
    public function __construct($fieldName, $dqlAlias = null)
    {
        $this->fieldName = $fieldName;
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
        if (null !== $this->dqlAlias) {
            $dqlAlias = $this->dqlAlias;
        }

        return sprintf('IDENTITY(%s.%s)', $dqlAlias, $this->fieldName);
    }

//    public function __toString()
//    {
//        return sprintf('IDENTITY(%s.%s)', $this->dqlAlias, $this->fieldName);
//    }
}