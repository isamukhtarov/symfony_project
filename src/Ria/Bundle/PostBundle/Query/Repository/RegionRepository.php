<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Query\Repository;

use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Ria\Bundle\PostBundle\Entity\Region\Region;
use Ria\Bundle\PostBundle\Query\Hydrator\RegionHydrator;
use Ria\Bundle\CoreBundle\Repository\AbstractRepository;
use Ria\Bundle\PostBundle\Query\ViewModel\RegionViewModel;

/**
 * Class RegionRepository
 * @package Ria\Bundle\PostBundle\Query\Repository
 */
class RegionRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Region::class);
    }

    public function getBySlug(string $slug, string $language): ?RegionViewModel
    {
        $queryBuilder = $this->createQueryBuilder('r');

        return $queryBuilder
            ->select(['r.id', 'rt.slug', 'rt.title'])
            ->innerJoin('r.translations', 'rt', 'WITH', 'rt.language = :lang')
            ->where('rt.slug = :slug')
            ->setParameters(['lang' => $language, 'slug' => $slug])
            ->getQuery()
            ->getOneOrNullResult(RegionHydrator::HYDRATION_MODE);
    }

    public function getAll(string $language): array
    {
        return $this->createQueryBuilder('r')
            ->select(['r.id', 'rt.title', 'rt.slug'])
            ->innerJoin('r.translations', 'rt', 'WITH', 'rt.language = :lang')
            ->setParameters(['lang' => $language])
            ->getQuery()
            ->getResult();
    }

    public function getApiList(string $language): array
    {
        $queryBuilder = $this->createQueryBuilder('r');

        return $queryBuilder
            ->select(['rt.slug as index', 'rt.title as name'])
            ->innerJoin('r.translations', 'rt', 'WITH', 'rt.language = :language')
            ->setParameters(['language' => $language])
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);
    }
}