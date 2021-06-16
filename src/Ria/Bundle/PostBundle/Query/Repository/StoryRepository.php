<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Query\Repository;

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Ria\Bundle\PostBundle\Entity\Story\Story;
use Ria\Bundle\PostBundle\Query\Hydrator\StoryHydrator;
use Ria\Bundle\CoreBundle\Repository\AbstractRepository;
use Ria\Bundle\PostBundle\Query\ViewModel\StoryViewModel;
use Ria\Bundle\CoreBundle\Query\EntitySpecificationRepositoryTrait;

class StoryRepository extends AbstractRepository
{
    use EntitySpecificationRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Story::class);
    }

    function getSpecsNamespace(): string
    {
        return "Ria\\Bundle\\PostBundle\\Query\\Specifications\Story";
    }

    public function getAll(string $language): array
    {
        return $this->createQueryBuilder('s')
            ->select('s')
            ->join('s.translations', 'st', Join::WITH, 'st.language = :language')
            ->where('s.status = :status')
            ->setParameters(['language' => $language, 'status' => true])
            ->orderBy('s.id', 'DESC')
            ->getQuery()
            ->execute();
    }

    public function list(string $language, int $limit): array
    {
        return $this->createQueryBuilder('s')
            ->select(['s.id', 't.title', 't.slug'])
            ->innerJoin('s.translations', 't', 'with', 't.language = :language')
            ->where('s.status = :status')
            ->orderBy('s.created_at', 'desc')
            ->setParameters([':status' => true, ':language' => $language])
            ->setMaxResults($limit)
            ->getQuery()
            ->execute();
    }

    public function findBySlug(string $slug, string $language): ?StoryViewModel
    {
        return $this->createQueryBuilder('s')
            ->select(['s.id', 't.title', 't.slug', 't.description', 't.language', 'p.filename as cover'])
            ->innerJoin('s.translations', 't', 'with', 't.language = :language')
            ->leftJoin('s.cover', 'p')
            ->where('t.slug = :slug')
            ->setParameters([
                'slug' => $slug,
                'language' => $language
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult(StoryHydrator::HYDRATION_MODE);
    }

    public function searchByTitle(string $language, string $title): mixed
    {
        $query = $this->createQueryBuilder('s');

        return $query
            ->select(['s.id AS id', 'tr.title AS text'])
            ->join('s.translations', 'tr')
            ->where($query->expr()->like('tr.title', ':title'))
            ->andWhere('s.status = :status')
            ->andWhere('tr.language = :language')
            ->setParameters([
                'title' => "%$title%",
                'status' => true,
                'language' => $language
            ])
            ->getQuery()
            ->execute();
    }
}