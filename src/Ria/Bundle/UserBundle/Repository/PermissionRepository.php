<?php

declare(strict_types=1);

namespace Ria\Bundle\UserBundle\Repository;

use Ria\Bundle\UserBundle\Entity\Permission;
use Doctrine\Persistence\ManagerRegistry;
use Ria\Bundle\CoreBundle\Repository\AbstractRepository;

class PermissionRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Permission::class);
    }
}