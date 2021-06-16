<?php

namespace Ria\Bundle\DataGridBundle\Grid\ItemListNormalizer;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class StandardNormalizer implements NormalizerInterface
{
    public function normalize(Query $query, QueryBuilder $queryBuilder, string $hydratorClass = null): array
    {
        /*
         * Add custom hydrator
         */
        $emConfig = $queryBuilder->getEntityManager()->getConfiguration();
        $hydrator = new \ReflectionClass($hydratorClass);
        $hydratorName = $hydrator->getShortName();
        $emConfig->addCustomHydrationMode($hydratorName, $hydratorClass);

        return $query->getResult($hydratorName);
    }
}
