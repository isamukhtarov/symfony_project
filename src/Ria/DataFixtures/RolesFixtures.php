<?php
declare(strict_types=1);

namespace Ria\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Ria\Bundle\UserBundle\Entity\Role;
use Ria\Bundle\UserBundle\Entity\User;

class RolesFixtures extends Fixture implements DependentFixtureInterface
{
    public const SuperAdministrator_ROLE_REFERENCE = 'SuperAdministrator';
    public const Administrator_ROLE_REFERENCE = 'Administrator';
    public const DepartmentHead_ROLE_REFERENCE = 'DepartmentHead';

    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function load(ObjectManager $manager)
    {
        $superAdministratorRole = $this->findOrCreate('SuperAdministrator');
        $adminRole              = $this->findOrCreate('Administrator');
        $departmentHeadRole     = $this->findOrCreate('DepartmentHead');
        $seniorNewsmanRole      = $this->findOrCreate('SeniorNewsman');
        $newsmanRole            = $this->findOrCreate('Newsman');
        $leadTranslatorRole     = $this->findOrCreate('LeadTranslator');
        $translatorRole         = $this->findOrCreate('Translator');
        $designerRole           = $this->findOrCreate('Designer');

        /** @var User $adminUser */
        $adminUser = $this->getReference(UserFixtures::ADMIN_USER_REFERENCE);
        $adminUser->sync('roles', new ArrayCollection([$superAdministratorRole]));

        $this->entityManager->persist($adminUser);
        $this->entityManager->flush();

        $this->addReference(self::SuperAdministrator_ROLE_REFERENCE, $superAdministratorRole);
        $this->addReference(self::Administrator_ROLE_REFERENCE, $adminRole);
        $this->addReference(self::DepartmentHead_ROLE_REFERENCE, $departmentHeadRole);
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class
        ];
    }

    private function findOrCreate(string $roleName): Role
    {
        $role = $this->entityManager
            ->getRepository(Role::class)
            ->findOneBy(['name' => $roleName]);

        if (!$role) {
            $role = (new Role())->setName($roleName);
            $this->entityManager->persist($role);
            $this->entityManager->flush();
        }

        return $role;
    }

}