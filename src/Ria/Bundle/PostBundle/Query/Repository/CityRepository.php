<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Query\Repository;

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Ria\Bundle\PostBundle\Entity\City\City;
use Ria\Bundle\PostBundle\Entity\Region\Region;
use Ria\Bundle\CoreBundle\Repository\AbstractRepository;

class CityRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, City::class);
    }

    public function getList(): array
    {
        return $this->createQueryBuilder('c')
            ->join('c.translations', 'ct', Join::WITH, 'ct.language = :language')
            ->setParameter('language', 'az')
            ->getQuery()
            ->execute();
    }

    public function getByRegion(int $regionId, string $language): array
    {
        return $this->createQueryBuilder('c')
            ->select('c.id', 'identity(c.region) as region_id', 'ct.title')
            ->join('c.translations', 'ct', Join::WITH, 'ct.language = :language')
            ->where('identity(c.region) = :region_id')
            ->setParameters(['language' => $language, 'region_id' => $regionId])
            ->getQuery()
            ->execute();
    }
}