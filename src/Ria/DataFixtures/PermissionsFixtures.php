<?php
declare(strict_types=1);

namespace Ria\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Ria\Bundle\UserBundle\Entity\Permission;
use Ria\Bundle\UserBundle\Entity\Role;

class PermissionsFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function load(ObjectManager $manager)
    {
        $manageUsersPermission   = $this->findOrCreate('manageUsers');
        $viewUsersPermission     = $this->findOrCreate('viewUsers');
        $manageStoriesPermission = $this->findOrCreate('manageStories');
        $manageCategoriesPermission = $this->findOrCreate('manageCategories');
        $canBeAuthor = $this->findOrCreate('canBeAuthor');
        $unblockPost = $this->findOrCreate('unblockPost');

        /** @var Role $superAdministratorRole */
        $superAdministratorRole = $this->getReference(RolesFixtures::SuperAdministrator_ROLE_REFERENCE);
        $superAdministratorRole->sync('permissions', new ArrayCollection([
            $manageUsersPermission, $viewUsersPermission, $manageStoriesPermission, $manageCategoriesPermission,
            $canBeAuthor, $unblockPost
        ]));

        $this->entityManager->persist($superAdministratorRole);
        $this->entityManager->flush();
    }

    public function getDependencies(): array
    {
        return [
            RolesFixtures::class
        ];
    }

    private function findOrCreate(string $permissionName): Permission
    {
        $permission = $this->entityManager
            ->getRepository(Permission::class)
            ->findOneBy(['name' => $permissionName]);

        if (!$permission) {
            $permission = (new Permission())->setName($permissionName);
            $this->entityManager->persist($permission);
            $this->entityManager->flush();
        }

        return $permission;
    }

}