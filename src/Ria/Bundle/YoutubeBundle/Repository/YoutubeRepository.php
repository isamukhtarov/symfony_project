<?php

declare(strict_types=1);

namespace Ria\Bundle\YoutubeBundle\Repository;

use Doctrine\Persistence\ManagerRegistry;
use Ria\Bundle\CoreBundle\Repository\AbstractRepository;
use Ria\Bundle\YoutubeBundle\Entity\YouTube;

class YoutubeRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, YouTube::class);
    }
}