<?php

declare(strict_types=1);

namespace Ria\Bundle\UserBundle\Repository;

use Ria\Bundle\UserBundle\Entity\Role;
use Doctrine\Persistence\ManagerRegistry;
use Ria\Bundle\CoreBundle\Repository\AbstractRepository;

/**
 * Class RoleRepository
 * @package Ria\Bundle\UserBundle\Repository
 */
class RoleRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Role::class);
    }

    public function getByPermission(string $permission): array
    {
        return $this->createQueryBuilder('r')
            ->join('r.permissions', 'p')
            ->where('p.name = :permission')
            ->setParameter('permission', $permission)
            ->getQuery()
            ->execute();
    }
}