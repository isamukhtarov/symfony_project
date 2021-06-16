<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Query\Specifications\Post;

use Doctrine\ORM\QueryBuilder;
use Happyr\DoctrineSpecification\Query\AbstractSelect;

/**
 * Class AddSelect
 * @package Ria\Bundle\PostBundle\Query\Specifications\Post
 */
class AddSelect extends AbstractSelect
{

    protected function modifySelection(QueryBuilder $qb, array $selections)
    {
        $qb->addSelect($selections);
    }
}
