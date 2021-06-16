<?php

declare(strict_types=1);

namespace Ria\Bundle\ConfigBundle\Repository;

use Doctrine\Persistence\ManagerRegistry;
use Ria\Bundle\ConfigBundle\Entity\Config;
use Ria\Bundle\CoreBundle\Repository\AbstractRepository;

class ConfigRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Config::class);
    }
}