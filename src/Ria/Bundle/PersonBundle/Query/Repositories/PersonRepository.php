<?php

declare(strict_types=1);

namespace Ria\Bundle\PersonBundle\Query\Repositories;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Ria\Bundle\CoreBundle\Query\EntitySpecificationRepositoryTrait;
use Ria\Bundle\PersonBundle\Entity\Person\Type;
use Ria\Bundle\PersonBundle\Entity\Person\Person;
use Ria\Bundle\PersonBundle\Query\Hydrator\PersonHydrator;
use Ria\Bundle\CoreBundle\Repository\AbstractRepository;
use Ria\Bundle\PersonBundle\Query\ViewModel\PersonViewModel;
use Ria\Bundle\PhotoBundle\Query\Hydrator\PhotoHydrator;
use Ria\Bundle\PhotoBundle\Query\ViewModel\PhotoViewModel;

class PersonRepository extends AbstractRepository
{

    use EntitySpecificationRepositoryTrait;

    public function getHydrationMode(): ?string
    {
        return PersonHydrator::HYDRATION_MODE;
    }

    public function getSpecsNamespace(): string
    {
        return "Ria\\Bundle\\PersonBundle\\Query\\Specifications";
    }

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Person::class);
    }

    public function getExperts(string $language): array
    {
        $expression = new Expr();
        return $this->createQueryBuilder('p')
            ->select(['p.id'])
            ->addSelect($expression->concat('pt.first_name', $expression->literal(' '), 'pt.last_name') . 'AS full_name')
            ->innerJoin('p.translations', 'pt', 'WITH', 'pt.language = :language')
            ->where('p.status = :status')
            ->andWhere('p.type.type = :type')
            ->orderBy('pt.last_name')
            ->setParameters(['language' => $language, 'status' => true, 'type' => Type::EXPERT])
            ->getQuery()
            ->execute();
    }

    public function list(string $q, string $language, string $type): array
    {
        $expression = new Expr();
        return $this->createQueryBuilder('p')
            ->select(['p.id AS id'])
            ->addSelect($expression->concat('tr.first_name', $expression->literal(' '), 'tr.last_name') . ' AS text')
            ->join('p.translations', 'tr', 'with', 'tr.language = :language')
            ->where($expression->like('tr.first_name', ':query'))
            ->orWhere($expression->like('tr.last_name', ':query'))
            ->andWhere('p.type.type = :type')
            ->setParameters(['query' => "%$q%", 'language' => $language, 'type' => $type])
            ->getQuery()
            ->execute();
    }

    public function getByIds(array $ids, string $language): array
    {
        $expression = new Expr();
        return $this->createQueryBuilder('p')
            ->select(['p.id as id'])
            ->addSelect($expression->concat('tr.first_name', $expression->literal(' '), 'tr.last_name') . ' AS name')
            ->join('p.translations', 'tr', 'with', 'tr.language = :language')
            ->where($expression->in('p.id', ':ids'))
            ->setParameters(compact('ids', 'language'))
            ->getQuery()
            ->execute();
    }

    public function getBySlug(string $slug, string $language): ?PersonViewModel
    {
        $queryBuilder = $this->createQueryBuilder('p');

        return $queryBuilder
            ->select(['p.id', 'ph.filename as thumb', 'pt.first_name', 'pt.last_name', 'pt.slug', 'pt.position', 'pt.text as timeline'])
            ->innerJoin('p.translations', 'pt', 'WITH', 'pt.language = :lang')
            ->leftJoin('p.photo', 'ph')
            ->where('pt.slug = :slug AND p.type.type = :type AND p.status = 1')
            ->setParameters(['slug' => $slug, 'lang' => $language, 'type' => Type::PERSON])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult(PersonHydrator::HYDRATION_MODE);
    }

    public function getExpertBySlug(string $slug, string $language): ?PersonViewModel
    {
        $queryBuilder = $this->createQueryBuilder('p');

        return $queryBuilder
            ->select(['p.id', 'ph.filename as thumb', 'pt.first_name', 'pt.last_name', 'pt.slug', 'pt.position', 'pt.text as timeline'])
            ->innerJoin('p.translations', 'pt', 'WITH', 'pt.language = :lang')
            ->leftJoin('p.photo', 'ph')
            ->where('pt.slug = :slug AND p.type.type = :type AND p.status = 1')
            ->setParameters(['slug' => $slug, 'lang' => $language, 'type' => Type::EXPERT])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult(PersonHydrator::HYDRATION_MODE);
    }

    /**
     * @param int $id
     * @return PhotoViewModel[]
     */
    public function getPersonPhotos(int $id): array
    {
        return $this->createQueryBuilder('p')
            ->select(['ph.id', 'ph.filename'])
            ->innerJoin('p.photos', 'ph')
            ->where('p.id = :id')
            ->orderBy('pp.sort', 'desc')
            ->setParameters(['id' => $id])
            ->getQuery()
            ->getResult(PhotoHydrator::HYDRATION_MODE);
    }

    public function get(string $language, string $filter = null): array
    {
        $queryBuilder = $this->createQueryBuilder('p');
        $this->applySpecifications($queryBuilder, [
            'select',
            ['type' => Type::PERSON],
            'withPhoto',
            [
                'withTranslation' => [
                    'language' => $language,
                    'filter'   => $filter,
                    'orderBy'  => 'last_name',
                ],
            ]
        ]);

        $queryBuilder->andWhere($queryBuilder->expr()->eq('p.status', true));

        return $queryBuilder->getQuery()->getResult(PersonHydrator::HYDRATION_MODE);
    }
}