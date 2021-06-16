<?php

declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Repository;

use Doctrine\Persistence\ManagerRegistry;
use Ria\Bundle\CoreBundle\Repository\AbstractRepository;
use Ria\Bundle\PostBundle\Entity\ExpertQuote\ExpertQuote;

class ExpertQuoteRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExpertQuote::class);
    }

    public function latestOne()
    {
        return $this->createQueryBuilder('eq')
            ->orderBy('eq.id', 'desc')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}