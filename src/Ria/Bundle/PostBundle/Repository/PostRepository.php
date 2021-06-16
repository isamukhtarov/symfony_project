<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Repository;

use DateTime;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Ria\Bundle\PostBundle\Entity\Post\Post;
use Ria\Bundle\PostBundle\Entity\Post\Speech;
use Ria\Bundle\PostBundle\Entity\Post\Status;
use Ria\Bundle\PostBundle\Entity\Post\Type;
use Ria\Bundle\PostBundle\Entity\Tag\Tag;
use Ria\Bundle\PostBundle\Entity\Tag\Translation;
use Ria\Bundle\PostBundle\Query\Hydrator\PostHydrator;
use Ria\Bundle\CoreBundle\Repository\AbstractRepository;
use Ria\Bundle\CoreBundle\Query\EntitySpecificationRepositoryTrait;
use Ria\Bundle\PersonBundle\Entity\Person\Translation as PersonTranslation;
use Ria\Bundle\PostBundle\Query\ViewModel\PostViewModel;
use Ria\Bundle\UserBundle\Entity\Translation as UserTranslation;

class PostRepository extends AbstractRepository
{
    use EntitySpecificationRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function getSpecsNamespace(): string
    {
        return "Ria\\Bundle\\PostBundle\\Query\\Specifications\Post";
    }

    public function getHydrationMode(): string
    {
        return PostHydrator::HYDRATION_MODE;
    }

    public function list(string $query, string $language, int $currentId = 0): array
    {
        $expression = new Expr();
        return $this->createQueryBuilder('p')
            ->select(['p.id AS id', 'p.title AS text'])
            ->where('p.language = :language')
            ->andWhere($expression->like('p.title', ':title'))
            //->andWhere($expression->gt('p.publishedAt', ':time'))
            ->andWhere($expression->neq('p.id', ':current_id'))
            ->orderBy('p.publishedAt', 'DESC')
            ->setMaxResults(10)
            ->setParameters([
                'language' => $language,
                'title' => "%$query%",
                //'time'       => time(),
                'current_id' => $currentId,
            ])
            ->getQuery()
            ->execute();
    }

    public function getByIds(array $ids): array
    {
        return $this->createQueryBuilder('p')
            ->select(['p.id AS id', 'p.title AS title'])
            ->andWhere((new Expr())->in('p.id', ':ids'))
            ->setParameters(['ids' => $ids])
            ->getQuery()
            ->execute();
    }

    public function getOtherTranslations(int $parent, string $excludedLang): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.parent = :parent and p.isPublished = 1', 'p.language <> :excludeLang')
            ->setParameters(['parent' => $parent, 'excludeLang' => $excludedLang])
            ->orderBy('p.publishedAt', 'DESC')
            ->setMaxResults(2)
            ->getQuery()
            ->execute();
    }

    public function getByCategories(string $language, array|int $categories, int $limit, array $filters = []): array
    {
        $queryBuilder = $this->createQueryBuilder('p');
        $this->applySpecifications($queryBuilder, array_merge([
            'select',
            ['lang' => $language],
            ['category' => $categories],
            'latest',
            'withCategory',
            'withPhoto',
            ['limit' => $limit],
            'published',
        ], $filters));

        $query = $queryBuilder->addSelect(['p.type.type', 'p.description']);

        return $query->getQuery()->getResult(PostHydrator::HYDRATION_MODE);
    }

    public function getArticlesAndOpinions(array $params): array
    {
        $query = $this->createQueryBuilder('p');

        return $query->select([
            'p.id', 'IDENTITY(p.category)', 'p.title', 'p.type.type', 'p.slug', 'p.publishedAt', 'p.description', 'p.language',
            'pph.filename as image',
            'et.first_name as expert_first_name', 'et.last_name as expert_last_name',
            'et.position as expert_position', 'et.slug as expert_slug', 'eph.filename as expert_thumb',
            'aph.filename as author_thumb',
            'c.id as category_id', 'ct.title as category_name', 'ct.slug as category_url',
            'at.firstName as authorFirstName', 'at.lastName as authorLastName', 'at.position as author_position', 'at.slug as author_slug',
        ])
            ->innerJoin('p.category', 'c')
            ->innerJoin('c.translations', 'ct', 'WITH', 'ct.language = :lang')
            ->leftJoin('p.author', 'a')
            ->leftJoin(UserTranslation::class, 'at', Join::WITH, 'IDENTITY(at.user) = a.id and at.language = :lang')
            ->leftJoin('p.expert', 'e')
            ->leftJoin(PersonTranslation::class, 'et', Join::WITH, 'IDENTITY(et.person) = e.id and et.language = :lang')
            ->leftJoin('p.photo', 'pph')
            ->leftJoin('a.photo', 'aph')
            ->leftJoin('e.photo', 'eph')
            ->where('p.isPublished = 1')
            ->andWhere('p.language = :lang')
            ->andWhere('p.type.type in (:types)')
            ->orderBy('p.publishedAt', 'DESC')
            ->setMaxResults($params['limit'])
            ->setParameters([':lang' => $params['lang'], ':types' => [Type::ARTICLE, Type::OPINION]])
            ->getQuery()
            ->setHydrationMode(PostHydrator::HYDRATION_MODE)
            ->execute();
    }

    public function getLastPosts(string $language, int $limit, ?int $page = null, array $addSpecs = []): array
    {
        $specifications = [
            [
                'select' => [
                    'id', 'title', 'slug', 'type.type', 'description', 'youtubeId',
                    'publishedAt', 'markedWords',
                    'isImportant', 'isMain'
                ]
            ],
            ['withCategory' => $language],
            'withPhoto',
            ['lang' => $language],
            'latest',
            'published',
            ['limit' => $limit]
        ];

        if ($page) {
            $addSpecs[] = ['offset' => ($page - 1) * $limit];
        }

        return $this->match(array_merge($specifications, $addSpecs), PostHydrator::HYDRATION_MODE);
    }

    public function getOtherTranslates(int $parent, string $excludeLanguage): array
    {
        $queryBuilder = $this->createQueryBuilder('p');

        $query = $queryBuilder
            ->select([
                'p.id', 'p.title', 'p.slug', 'p.description', 'ph.filename as image', 'p.publishedAt',
                'p.language', 'p.type.type',
                'ct.title as category_title', 'ct.slug as category_slug',
            ])
            ->innerJoin('p.category', 'c')
            ->innerJoin('c.translations', 'ct', 'WITH', 'ct.language = p.language')
            ->leftJoin('p.photo', 'ph')
            ->where('p.parent = :parent and p.isPublished = 1', 'p.language <> :excludeLang')
            ->setParameters(['parent' => $parent, 'excludeLang' => $excludeLanguage])
            ->orderBy('p.publishedAt', 'DESC')
            ->setMaxResults(2);

        return $query->getQuery()->getResult(PostHydrator::HYDRATION_MODE);
    }

    public function getElasticPosts(int $limit, string $dateFrom): array
    {
        $query = $this->createQueryBuilder('p')
            ->innerJoin('p.category', 'c')
            ->innerJoin('c.translations', 'ct', 'WITH', 'ct.language = p.language')
            ->leftJoin('p.photo', 'ph')
            ->where('p.publishedAt > :dateFrom and p.isPublished = 1')
            ->setParameter('dateFrom', $dateFrom)
            ->orderBy('p.publishedAt', 'asc')
            ->setMaxResults($limit);

        return $query->getQuery()->getResult();
    }

    public function getById(int $id, string $language): ?PostViewModel
    {
        $queryBuilder = $this->createQueryBuilder('p');

        /** @var PostViewModel|null $post */
        $post = $queryBuilder
            ->select([
                'p.id', 'IDENTITY(p.parent) parent', 'p.language', 'p.status.status', 'p.title', 'p.content', 'p.description', 'p.slug',
                'pph.filename as image', 'p.publishedAt', 'p.isPublished', 'p.createdAt', 'p.updatedAt', 'p.views', 'p.markedWords',
                'p.youtubeId', 'p.type.type', 'p.meta.meta', 'p.linksNoIndex',
                'IDENTITY(p.category) as category_id', 'ct.title as category_title', 'ct.slug as category_slug',
                'CONCAT(ut.firstName, \' \', ut.lastName) as author_name', 'ut.slug as author_slug', 'uph.filename as author_thumb',
                'speech.filename as speech_filename'
            ])
            ->innerJoin('p.category', 'c')
            ->innerJoin('c.translations', 'ct', 'WITH', 'ct.language = :lang')
            ->innerJoin('p.author', 'u')
            ->innerJoin('u.translations', 'ut', 'WITH', 'ut.language = :lang')
            ->leftJoin(Speech::class, 'speech', 'WITH', 'speech.post = p.id')
            ->leftJoin('p.photo', 'pph')
            ->leftJoin('u.photo', 'uph')
            ->where('p.id = :id')
            ->setParameters(['id' => $id, 'lang' => $language])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult(PostHydrator::HYDRATION_MODE);

        if (!empty($post)) {
            $post->tags = $this->getTagsByPost($post->id, $post->language);
        }

        return $post;
    }

    public function getBySlug(string $slug, string $language): ?PostViewModel
    {
        $queryBuilder = $this->createQueryBuilder('p');

        /** @var PostViewModel|null $post */
        $post = $queryBuilder
            ->select([
                'p.id', 'IDENTITY(p.parent) as parent', 'p.language', 'p.status.status', 'p.title', 'p.content', 'p.description', 'p.slug',
                'pph.filename as image', 'IDENTITY(p.photo) as photoId', 'p.publishedAt', 'p.isPublished', 'p.createdAt', 'p.updatedAt', 'p.views', 'p.markedWords',
                'p.youtubeId', 'p.type.type', 'p.meta.meta', 'p.linksNoIndex',
                'IDENTITY(p.category) as category_id', 'ct.title as category_title', 'ct.slug as category_slug',
                'CONCAT(ut.firstName, \' \', ut.lastName) as author_name', 'ut.slug as author_slug', 'uph.filename as author_thumb',
                'speech.filename as speech_filename'
            ])
            ->innerJoin('p.category', 'c')
            ->innerJoin('c.translations', 'ct', 'WITH', 'ct.language = :lang')
            ->innerJoin('p.author', 'u')
            ->innerJoin('u.translations', 'ut', 'WITH', 'ut.language = :lang')
            ->leftJoin(Speech::class, 'speech', 'WITH', 'speech.post = p.id')
            ->leftJoin('p.photo', 'pph')
            ->leftJoin('u.photo', 'uph')
            ->where('p.slug = :slug and p.language = :lang')
            ->andWhere(
                $queryBuilder->expr()->in(
                    'p.status.status',
                    array_merge(Status::publishedOnes(), [Status::PRIVATE])
                )
            )
            ->setParameters(['slug' => $slug, 'lang' => $language])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult(PostHydrator::HYDRATION_MODE);

        if (!empty($post)) {
            $post->tags = $this->getTagsByPost($post->id, $language);
        }

        return $post;
    }

    public function getByPerson(int $id, string $language, int $limit): array
    {
        return $this->createQueryBuilder('p')
            ->select([
                'p.id', 'p.title', 'ph.filename as image', 'p.publishedAt',
                'p.content as content', 'p.slug as slug', 'p.markedWords',
                'ct.slug as category_slug'
            ])
            ->innerJoin('p.persons', 'pp', 'WITH', 'pp.id = :person')
            ->leftJoin('p.photo', 'ph')
            ->innerJoin('p.category', 'c')
            ->innerJoin('c.translations', 'ct', 'WITH', 'ct.language = :lang')
            ->where('p.isPublished = 1 AND p.language = :lang')
            ->orderBy('p.publishedAt', 'DESC')
            ->setParameters(['person' => $id, 'lang' => $language])
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult(PostHydrator::HYDRATION_MODE);
    }

    public function getPrevious(int $categoryId, DateTime $publishedAt, string $language): ?PostViewModel
    {
        $queryBuilder = $this->createQueryBuilder('p');

        $query = $queryBuilder
            ->select([
                'p.id', 'IDENTITY(p.parent) as parent', 'p.language', 'p.status.status', 'p.title', 'p.content', 'p.description', 'p.slug',
                'pph.filename as image', 'p.publishedAt', 'p.isPublished', 'p.createdAt', 'p.updatedAt', 'p.views', 'p.markedWords',
                'p.youtubeId', 'p.type.type', 'p.meta.meta',
                'IDENTITY(p.category) as category_id', 'ct.title as category_title', 'ct.slug as category_slug',
                'CONCAT(ut.firstName, \' \', ut.lastName) as author_name', 'ut.slug as author_slug', 'uph.filename as author_thumb',
                'speech.filename as speech_filename'
            ])
            ->innerJoin('p.category', 'c')
            ->innerJoin('c.translations', 'ct', 'WITH', 'ct.language = :lang')
            ->innerJoin('p.author', 'u')
            ->innerJoin('u.translations', 'ut', 'WITH', 'ut.language = :lang')
            ->leftJoin('p.photo', 'pph')
            ->leftJoin('u.photo', 'uph')
            ->leftJoin(Speech::class, 'speech', 'WITH', 'speech.post = p.id')
            ->where('p.category = :category_id and p.language = :lang and p.isPublished = 1')
            ->andWhere($queryBuilder->expr()->in('p.status.status', Status::publishedOnes()))
            ->setParameters(['category_id' => $categoryId, 'lang' => $language])
            ->setMaxResults(1)
            ->addOrderBy('p.publishedAt', 'DESC');

        if ($publishedAt->getTimestamp() > (time() - 86400 * 21)) {
            $query->andWhere('p.publishedAt < :publishedAt')->setParameter('publishedAt', $publishedAt);
        }

        return $query->getQuery()->getOneOrNullResult(PostHydrator::HYDRATION_MODE);
    }

    public function getTagsByPost(int $postId, string $language): array
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select(['t.id', 'tt.name', 't.slug', 'tt.language'])
            ->from(Tag::class, 't')
            ->innerJoin(Translation::class, 'tt', 'WITH', 'tt.tag = t.id AND tt.language = :lang')
            ->innerJoin('t.posts', 'pt', 'WITH', 'pt.id = :post')
            ->where('tt.count > 2')
            ->setParameters([':post' => $postId, 'lang' => $language])
            ->getQuery()
            ->execute();
    }

    public function getByAuthor(int $authorId, string $language, int $limit, ?string $timestamp = null): array
    {
        $queryBuilder = $this->createQueryBuilder('p');

        $specifications = [
            'select',
            'published',
            'latest',
            ['lang' => $language],
            ['limit' => $limit],
            ['withCategory' => $language],
            ['withAuthor' => ['id' => $authorId, 'language' => $language]],
            'withPhoto'
        ];

        if ($timestamp) {
            $specifications[] = ['dateLess' => $timestamp];
        }

        $this->applySpecifications($queryBuilder, $specifications);

        return $queryBuilder->getQuery()->getResult(PostHydrator::HYDRATION_MODE);
    }

    public function getByTag(int $tagId, string $language, int $limit, ?string $timestamp = null): array
    {
        $queryBuilder = $this->createQueryBuilder('p');

        $this->applySpecifications($queryBuilder, [
            'select',
            ['lang'  => $language],
            'latest',
            ['withCategory' => $language],
            ['limit' => $limit],
            'published',
            empty($timestamp) ? null : ['dateLess' => $timestamp]
        ]);

        $query = $queryBuilder
            ->addSelect(['p.type.type', 'p.description'])
            ->innerJoin('p.tags', 'pt', 'WITH', 'pt.id = :tag_id')
            ->setParameter('tag_id', $tagId);

        return $query->getQuery()->getResult(PostHydrator::HYDRATION_MODE);
    }

    public function getByExpert(int $expertId, string $language, int $limit, ?string $timestamp = null): array
    {
        return $this->getLastPosts($language, $limit, null, [
            ['withExpert' => ['language' => $language, 'id' => $expertId]],
            $timestamp ? ['dateLess' => $timestamp] : null
        ]);
    }

    public function getByRange(int $start, int $limit, string $language, $hydrationMode = PostHydrator::HYDRATION_MODE): ?array
    {
        $queryBuilder = $this->createQueryBuilder('p');

        $query = $queryBuilder
            ->select([
                'p.id', 'p.updatedAt', 'p.language as lang', 'p.slug', /*'p.photo', 'p.parent',*/
                'p.publishedAt as created', 'p.updatedAt as updated', 'p.title', 'p.description',
                'ct.title as category_name', 'ct.slug as category_slug'
            ])
            ->innerJoin('p.category', 'c')
            ->innerJoin('c.translations', 'ct', 'WITH', 'ct.language = :language')
            ->where('p.language = :language')
            ->andWhere('p.isPublished = :isPublished')
            ->setFirstResult($start)
            ->setMaxResults($limit)
            ->orderBy('p.publishedAt', 'DESC')
            ->setParameter('language', $language)
            ->setParameter('isPublished', true);

        return $query->getQuery()->getResult($hydrationMode);
    }

    public function getCount(string $language)
    {
        return $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->where('p.language = :language')
            ->andWhere('p.isPublished = :isPublished')
            ->setParameter('language', $language)
            ->setParameter('isPublished', true)
            ->getQuery()
            ->getSingleScalarResult();
    }
}