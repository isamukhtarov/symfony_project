<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Query\Repository;

use Doctrine\ORM\Query\Expr\Join;
use Happyr\DoctrineSpecification\Spec;
use Doctrine\Persistence\ManagerRegistry;
use Ria\Bundle\CoreBundle\Query\EntitySpecificationRepositoryTrait;
use Ria\Bundle\CoreBundle\Repository\AbstractRepository;
use Ria\Bundle\PostBundle\Entity\Category\Category;
use Ria\Bundle\PostBundle\Query\Hydrator\CategoryHydrator;
use Ria\Bundle\PostBundle\Query\Hydrator\PostHydrator;
use Ria\Bundle\PostBundle\Query\ViewModel\CategoryViewModel;

class CategoryRepository extends AbstractRepository
{

    use EntitySpecificationRepositoryTrait;

    public function getSpecsNamespace(): string
    {
        return "Ria\\Bundle\\PostBundle\\Query\\Specifications\Category";
    }

    /**
     * @return string
     */
    public function getHydrationMode()
    {
        return CategoryHydrator::HYDRATION_MODE;
    }

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function getAll(string $language): array
    {
        return $this->createQueryBuilder('c')
            ->join('c.translations', 'ct', Join::WITH, 'ct.language = :language')
            ->orderBy('c.sort', 'ASC')
            ->setParameter('language', $language)
            ->andWhere('c.parent IS NULL')
            ->getQuery()
            ->execute();
    }

    public function menuItems(string $language, int $limit): array
    {
        $queryBuilder = $this->createQueryBuilder('c');

        return $queryBuilder
            ->select(['c.id', 'ct.title', 'ct.slug'])
            ->innerJoin('c.translations', 'ct', 'WITH', 'ct.language = :lang')
            ->where($queryBuilder->expr()->isNull('c.parent'))
            ->andWhere('c.status = :status')
            ->setParameters(['lang' => $language, 'status' => true])
            ->orderBy('c.sort')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult(CategoryHydrator::HYDRATION_MODE);
    }

    public function get(string $language): array
    {
        $queryBuilder = $this->createQueryBuilder('c');

        return $queryBuilder
            ->select(['c.id', 'ct.title', 'ct.slug'])
            ->innerJoin('c.translations', 'ct', 'WITH', 'ct.language = :lang')
            ->where('c.status = :status')
            ->setParameters(['lang' => $language, 'status' => true])
            ->orderBy('c.sort')
            ->getQuery()
            ->execute();
    }

    public function getBySlug(string $slug, string $language): ?CategoryViewModel
    {
        $queryBuilder = $this->createQueryBuilder('c');

        return $queryBuilder
            ->select(['c.id', 'IDENTITY(c.parent) as parent_id', 'ct.title', 'ct.slug', 'ct.meta.meta',
                'cpt.title as parent_title', 'cpt.slug as parent_slug', 'ct.language as language'])
            ->innerJoin('c.translations', 'ct', 'WITH', 'ct.language = :lang')
            ->leftJoin('c.parent', 'cp')
            ->leftJoin('cp.translations', 'cpt', 'WITH', 'cpt.language = :lang')
            ->where('ct.slug = :slug AND c.status = :status')
            ->setParameters(['lang' => $language, 'status' => true, 'slug' => $slug])
            ->getQuery()
            ->getOneOrNullResult(CategoryHydrator::HYDRATION_MODE);
    }

    public function getById(int $id, string $language): ?CategoryViewModel
    {
        $queryBuilder = $this->createQueryBuilder('c');

        return $queryBuilder
            ->select(['c.id', 'IDENTITY(c.parent) as parent_id', 'ct.title', 'ct.slug', 'ct.meta.meta',
                'cpt.title as parent_title', 'cpt.slug as parent_slug', 'ct.language as language'])
            ->innerJoin('c.translations', 'ct', 'WITH', 'ct.language = :lang')
            ->leftJoin('c.parent', 'cp')
            ->leftJoin('cp.translations', 'cpt', 'WITH', 'cpt.language = :lang')
            ->where('c.id = :id AND c.status = :status')
            ->setParameters(['lang' => $language, 'status' => true, 'id' => $id])
            ->getQuery()
            ->getOneOrNullResult(CategoryHydrator::HYDRATION_MODE);
    }

    public function getParentCategories(string $language): array
    {
        $queryBuilder = $this->createQueryBuilder('c');

        return $queryBuilder
            ->select(['c.id', 'IDENTITY(c.parent) as parent_id', 'ct.title', 'ct.slug'])
            ->innerJoin('c.translations', 'ct', 'WITH', 'ct.language = :lang')
            ->where('c.parent = :parent AND c.status = :status')
            ->setParameters(['lang' => $language, 'status' => true, 'parent' => null])
            ->getQuery()
            ->getResult(CategoryHydrator::HYDRATION_MODE);
    }

    public function getParentCategoriesWithExcluded(string $language, ?int $id = null): array
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->leftJoin('c.translations', 'ct', 'WITH', 'ct.language = :lang')
            ->where('c.parent is NULL')
            ->andWhere('c.status = :status')
            ->setParameters(['lang' => $language, 'status' => true]);

        if ($id)
            $queryBuilder
                ->andWhere('c.id != :id')
                ->setParameter('id', $id);

        return $queryBuilder
                ->getQuery()
                ->getResult();
    }

    public function getSubCategories(int $parentId, string $language)
    {
        $queryBuilder = $this->createQueryBuilder('c');

        return $queryBuilder
            ->select(['c.id', 'IDENTITY(c.parent) as parent_id', 'ct.title', 'ct.slug'])
            ->innerJoin('c.translations', 'ct', 'WITH', 'ct.language = :lang')
            ->where('c.parent = :parent AND c.status = :status')
            ->setParameters(['lang' => $language, 'status' => true, 'parent' => $parentId])
            ->getQuery()
            ->getResult(CategoryHydrator::HYDRATION_MODE);
    }

    public function getChildren(int|array $parentIds)
    {
        $queryBuilder = $this->createQueryBuilder('c');

        return $queryBuilder
            ->select(['c.id'])
            ->where($queryBuilder->expr()->in('c.parent', ':parent_ids'))
            ->andWhere('c.status = :status')
            ->setParameters([
                'parent_ids' => array_wrap($parentIds),
                'status' => true
            ])
            ->getQuery()
            ->execute();
    }

    public function getAllWithChildren($language, $limit = null, $parent_id = null)
    {
        $queryBuilder = $this->createQueryBuilder('c');

        $query = $queryBuilder
            ->innerJoin('c.translations', 'ct', 'WITH', 'ct.language = :lang')
            ->where('c.status = :status')
            ->setParameter('lang', $language)
            ->setParameter('status', true)
            ->orderBy('c.sort')
            ->setMaxResults($limit);

        if ($parent_id) {
            $query->andWhere('IDENTITY(c.parent) = :parent_id')
                ->setParameter('parent_id', $parent_id);
        } else {
            $query->andWhere($queryBuilder->expr()->isNull('c.parent'));
        }

        $categories = $query->getQuery()->getResult();

        $categories = collect($categories)
            ->transform(function (Category $category) use ($language) {
                return [
                  'id' => (string) $category->getId(),
                  'parent_id' => $category->getParent() ? (string) $category->getParent()->getId() : "0",
                  'name' => $category->getTranslation($language)->getTitle(),
                  'slug' => $category->getTranslation($language)->getSlug(),
                  'url' => $category->getTranslation($language)->getSlug(),
                  'parent_in_url' => "0",
                  'category_parent_id' => $category->getParent() ? (string) $category->getParent()->getId() : null,
                  'category_parent_group' => $category->getParent() ? (string) $category->getParent()->getId() : null,
                  'category_parent_name' => $category->getParent() ? $category->getParent()->getTranslation($language)->getTitle() : null,
                  'category_parent_slug' => $category->getParent() ? $category->getParent()->getTranslation($language)->getSlug() : null,
                  'category_parent_url' => $category->getParent() ? $category->getParent()->getTranslation($language)->getSlug() : null,
                  'category_parent_lang' => $category->getParent() ? $language : null,
                ];
            })->toArray();

        if (!$parent_id) {
            foreach ($categories as &$category) {
                $category['sub'] = $this->getAllWithChildren($language, $limit, $category['id']);
            }
        }

        return $categories;
    }

    public function getForPosts($ids, string $language)
    {
        $specs = Spec::andX(
            Spec::in('id', array_wrap($ids)),
            Spec::innerJoin('translations', 'ct'),
            Spec::eq('language', $language, 'ct'),
            Spec::select(
                Spec::selectAs(Spec::field('id'), 'category_id'),
                Spec::selectAs(Spec::field('title', 'ct'), 'category_title'),
                Spec::selectAs(Spec::field('slug', 'ct'), 'category_slug'),
            ),
        );

        return $this->match($specs);
    }

    public function getLastOrder(): int
    {
        $queryBuilder = $this->createQueryBuilder('c');

        $result = $queryBuilder
                ->select('c.sort')
                ->orderBy('c.sort', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

        return $result  ? $result['sort'] : 0;
    }

}