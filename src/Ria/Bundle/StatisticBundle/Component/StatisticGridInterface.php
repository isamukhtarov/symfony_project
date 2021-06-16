<?php

declare(strict_types=1);

namespace Ria\Bundle\StatisticBundle\Component;

use Doctrine\ORM\QueryBuilder;

interface StatisticGridInterface
{
    public function getQueryBuilder(string $locale): QueryBuilder;
}