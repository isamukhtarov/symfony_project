<?php

declare(strict_types=1);

namespace Ria\Bundle\CurrencyBundle\Repository;

use DateTime;
use Doctrine\DBAL\Types\DateTimeType;
use Doctrine\Persistence\ManagerRegistry;
use Ria\Bundle\CoreBundle\Repository\AbstractRepository;
use Ria\Bundle\CurrencyBundle\Entity\Currency;

class CurrencyRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Currency::class);
    }

    public function getExisting(array $codes, string $date): ?array
    {
        $query = $this->createQueryBuilder('c', 'c.code');
        return $query->where($query->expr()->in('c.code', $codes))
            ->andWhere($query->expr()->eq('c.date', $query->expr()->literal($date)))
            ->getQuery()
            ->execute();
    }

    public function getPrevious(Currency $currency): ?Currency
    {
        $query = $this->createQueryBuilder('c', 'c.code');
        return $query
            ->where($query->expr()->eq('c.code', $query->expr()->literal($currency->getCode())))
            ->andWhere($query->expr()->lt('c.date', $query->expr()->literal($currency->getDate()->format('Y-m-d'))))
            ->orderBy('c.date', 'DESC')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function lastCommonRecords(): array
    {
        $records = [];
        foreach (['USD', 'EUR', 'RUB', 'BRENT'] as $code) {
            $records[] = $this->createQueryBuilder('c')
                ->select(['c.code', 'c.value'])
                ->where('c.code = :code')
                ->setParameter('code', $code)
                ->addOrderBy('c.id', 'desc')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
        }
        return array_filter($records);
    }

    public function lastByDate(string $date): array
    {
        return $this->createQueryBuilder('c')
            ->select(['c.value', 'c.nominal', 'c.code', 'c.difference', 'c.date'])
            ->where('c.date = :date')
            ->setParameters([':date' => $date])
            ->getQuery()
            ->execute();
    }

    public function getLastDate(): ?DateTime
    {
        $qb     = $this->createQueryBuilder('c');
        $record = $qb
            ->select(['c.date'])
            ->where($qb->expr()->neq('c.code', $qb->expr()->literal('brent')))
            ->addOrderBy('c.date', 'desc')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        return $record['date'] ?? null;
    }
}