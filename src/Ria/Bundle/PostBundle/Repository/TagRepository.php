<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Repository;

use Doctrine\ORM\Query\Expr;
use Ria\Bundle\PostBundle\Entity\Tag\Tag;
use Doctrine\Persistence\ManagerRegistry;
use Ria\Bundle\CoreBundle\Repository\AbstractRepository;
use Ria\Bundle\PostBundle\Query\Hydrator\TagHydrator;
use Ria\Bundle\PostBundle\Query\ViewModel\TagViewModel;

class TagRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    public function list(string $q, string $language): array
    {
        return $this->createQueryBuilder('t')
            ->select(['tr.name AS id', 'tr.name AS text'])
            ->join('t.translations', 'tr')
            ->where((new Expr())->like('tr.name', ':name'))
            ->andWhere('tr.language = :language')
            ->setParameters(['name' => "%$q%", 'language' => $language])
            ->orderBy('tr.count', 'DESC')
            ->getQuery()
            ->execute();
    }

    public function getBySlug(string $slug, string $language): ?TagViewModel
    {
        $queryBuilder = $this->createQueryBuilder('t');

        return $queryBuilder
            ->select(['t.id', 'tt.name', 't.slug'])
            ->innerJoin('t.translations', 'tt', 'WITH', 'tt.language = :lang')
            ->where('t.slug = :slug')
            ->andWhere('tt.count > 2')
            ->setParameters(['lang' => $language, 'slug' => $slug])
            ->getQuery()
            ->getOneOrNullResult(TagHydrator::HYDRATION_MODE);
    }
}