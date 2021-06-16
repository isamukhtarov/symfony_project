<?php

declare(strict_types=1);

namespace Ria\Bundle\VoteBundle\Repository;

use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Ria\Bundle\CoreBundle\Query\EntitySpecificationRepositoryTrait;
use Ria\Bundle\CoreBundle\Repository\AbstractRepository;
use Ria\Bundle\VoteBundle\Entity\Option;
use Ria\Bundle\VoteBundle\Entity\Type;
use Ria\Bundle\VoteBundle\Entity\Vote;
use Ria\Bundle\VoteBundle\Query\Hydrator\VoteHydrator;
use Ria\Bundle\VoteBundle\Query\ViewModel\VoteViewModel;

class VoteRepository extends AbstractRepository
{
    use EntitySpecificationRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vote::class);
    }

    function getSpecsNamespace(): string
    {
        return "Ria\\Bundle\\PersonBundle\\Query\\Specifications";
    }

    public function getVotes(string $language): array
    {
        $qb = $this->createQueryBuilder('v');

        return $qb
            ->select(['v.id', 'v.showRecaptcha', 'v.title', 'v.endDate'])
            ->where('v.language = :language')
            ->andWhere('v.status = :status')
            ->andWhere(
                $qb->expr()->orX($qb->expr()->isNull('v.endDate'), 'v.endDate >= :date')
            )
            ->orderBy('v.createdAt','DESC')
            ->setParameters([
                'language' => $language,
                'status' => true,
                'date' => (new DateTime())->format('Y-m-d') . ' 00:00:00',
            ])
            ->getQuery()
            ->getResult();
    }

    public function getOnePrizeType(string $language): ?VoteViewModel
    {
        $qb = $this->createQueryBuilder('v');

        /** @var VoteViewModel|null $vote */
        $vote = $qb
            ->select(['v.id', 'v.title', 'ph.filename as image', 'v.endDate', 'p.id as post_id', 'p.slug as post_url', 'ct.slug as post_category_url'])
            ->leftJoin('v.photo', 'ph')
            ->leftJoin('v.posts', 'p')
            ->leftJoin('p.category', 'c')
            ->leftJoin('c.translations', 'ct', 'WITH', 'ct.language = :language')
            ->where('v.type.type = :type')
            ->andWhere('v.status = :status')
            ->andWhere('v.language = :language')
            ->andWhere(
                $qb->expr()->orX($qb->expr()->isNull('v.endDate'), 'v.endDate >= :date')
            )
            ->andWhere($qb->expr()->isNotNull('p.id'))
            ->setMaxResults(1)
            ->orderBy('v.createdAt', 'DESC')
            ->setParameters([
                'language' => $language,
                'status'   => true,
                'date'     => (new \DateTime())->format('Y-m-d') . ' 00:00:00',
                'type'     => Type::TYPE_PRIZE
            ])
            ->getQuery()
            ->getOneOrNullResult(VoteHydrator::HYDRATION_MODE);

        if ($vote) {
            $vote->options = $this->getVoteOptions($vote->id);
        }

        return $vote;
    }

    public function getById(int $id): ?VoteViewModel
    {
        return $this->createQueryBuilder('v')
            ->select(['v.id', 'v.showRecaptcha', 'v.title', 'v.startDate', 'v.endDate'])
            ->where('v.id = :id')
            ->setMaxResults(1)
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult(VoteHydrator::HYDRATION_MODE);
    }

    public function getForVotesPage(string $language, int $limit, ?int $lastId = null): array
    {
        $query = $this
            ->createQueryBuilder('v')
            ->select(['v.id', 'v.title', 'v.startDate', 'v.endDate'])
            ->where(
                'v.language = :lang',
                '(SELECT SUM(vo.score) FROM Ria\Bundle\VoteBundle\Entity\Option vo WHERE vo.vote = v.id) > 0'
            )
            ->setParameter('lang', $language)
            ->orderBy('v.id', 'DESC')
            ->setMaxResults($limit);

        if ($lastId) {
            $query->andWhere($query->expr()->lt('v.id', $lastId));
        }

        $votes = $query->getQuery()->getResult(VoteHydrator::HYDRATION_MODE);

        foreach ($votes as $vote) {
            $vote->options = $this->getVoteOptions($vote->id);
        }

        return $votes;
    }

    public function getVoteOptions(int $voteId): array
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select(['o.id', 'o.title', 'o.score'])
            ->from(Option::class, 'o')
            ->innerJoin('o.vote', 'v', 'WITH', 'v.id = :voteId')
            ->orderBy('o.sort')
            ->setParameter('voteId', $voteId)
            ->getQuery()
            ->execute();
    }

}