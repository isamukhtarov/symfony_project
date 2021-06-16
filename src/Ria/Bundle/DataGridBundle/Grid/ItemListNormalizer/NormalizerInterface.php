<?php

namespace Ria\Bundle\DataGridBundle\Grid\ItemListNormalizer;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

interface NormalizerInterface
{
    public function normalize(Query $query, QueryBuilder $queryBuilder, string $hydratorClass = null): array;

} 
