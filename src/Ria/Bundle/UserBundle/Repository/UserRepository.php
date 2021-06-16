<?php

declare(strict_types=1);

namespace Ria\Bundle\UserBundle\Repository;

use Doctrine\ORM\Query\Expr;
use Ria\Bundle\UserBundle\Entity\Role;
use Ria\Bundle\UserBundle\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Ria\Bundle\CoreBundle\Repository\AbstractRepository;
use Ria\Bundle\UserBundle\Query\Hydrator\UserHydrator;
use Ria\Bundle\UserBundle\Query\ViewModel\UserViewModel;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class UserRepository extends AbstractRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function getByPermission(string $permission, ?string $locale = null): array
    {
        // get permissions from users
        $fromUsers = $this->createQueryBuilder('u')
            ->join('u.permissions', 'p')
            ->where('u.status.status = :status')
            ->andWhere('p.name = :permission')
            ->setParameters(['permission' => $permission, 'status' => true]);

        if ($locale)
            $fromUsers->join('u.translations', 'ut')->andWhere('ut.language = :locale')->setParameter('locale', $locale);
        $fromUsers = $fromUsers->getQuery()->execute();

        // get permissions from roles
        $roles = $this->getEntityManager()
            ->createQueryBuilder()
            ->from(Role::class, 'r')
            ->select('r.name')
            ->join('r.permissions', 'p')
            ->where('p.name = :permission')
            ->setParameter('permission', $permission)
            ->getQuery()
            ->execute();

        return array_unique(array_replace(
            $this->getByRoles(array_column($roles, 'name')), $fromUsers
        ));
    }

    public function getByRoles(array $roles): array
    {
        return $this->createQueryBuilder('u')
            ->join('u.roles', 'r')
            ->where((new Expr())->in('r.name', ':roles'))
            ->andWhere('u.status.status = :status')
            ->setParameters(['roles' => $roles, 'status' => true])
            ->getQuery()
            ->execute();
    }

    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!($user instanceof User))
            throw new UnsupportedUserException();
        $user->setPassword($newEncodedPassword);
        $this->save($user);
    }

    /**
     * @param string $slug
     * @param string $language
     * @return UserViewModel|null
     * @throws NonUniqueResultException
     */
    public function getBySlug(string $slug, string $language): ?UserViewModel
    {
        $queryBuilder = $this->createQueryBuilder('u');

        return $queryBuilder
            ->select(['u.id', 'uph.filename as thumb', 'ut.slug', 'ut.firstName', 'ut.lastName', 'ut.description', 'ut.position'])
            ->innerJoin('u.translations', 'ut', 'WITH', 'ut.language = :lang')
            ->leftJoin('u.photo', 'uph')
            ->where('ut.slug = :slug and u.status.status = :status')
            ->setParameters(['lang' => $language, 'slug' => $slug, 'status' => true])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult(UserHydrator::HYDRATION_MODE);
    }
}