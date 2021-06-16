<?php

declare(strict_types=1);

namespace Ria\Bundle\WeatherBundle\Repository;

use Doctrine\Persistence\ManagerRegistry;
use Ria\Bundle\CoreBundle\Repository\AbstractRepository;
use Ria\Bundle\WeatherBundle\Entity\Weather;

class WeatherRepository extends AbstractRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Weather::class);
    }

    public function findOneByDate(string $date): ?Weather
    {
        return $this->createQueryBuilder('w')
            ->where('w.createdAt BETWEEN :start AND :end')
            ->setParameters(['start' => $date . ' 00:00:00', 'end' => $date . ' 23:59:59'])
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getLastOne(): ?array
    {
        return $this->createQueryBuilder('w')
            ->select(['w.id', 'w.data', 'w.createdAt'])
            ->orderBy('w.createdAt', 'desc')
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }
}