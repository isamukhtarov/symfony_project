<?php
declare(strict_types=1);

namespace Ria\Bundle\CoreBundle\Console\Command;

use Cocur\Slugify\Slugify;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Ria\Bundle\CoreBundle\Entity\Status;
use Ria\Bundle\PhotoBundle\Entity\Photo;
use Ria\Bundle\PhotoBundle\Entity\Translation as PhotoTranslation;
use Ria\Bundle\PhotoBundle\Service\ImagePackage;
use Ria\Bundle\UserBundle\Command\User\UserCommand;
use Ria\Bundle\UserBundle\Entity\Permission;
use Ria\Bundle\UserBundle\Entity\Role;
use Ria\Bundle\UserBundle\Entity\Translation;
use Ria\Bundle\UserBundle\Entity\User;
use Ria\Bundle\UserBundle\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Uid\Uuid;

class MigrateUsersCommand extends OldDataMigrationCommand
{

    private const ROLES = [
        14 => 'Administrator',
        15 => 'Newsman',
        16 => 'Translator',
        17 => 'Translator',
        18 => 'Translator',
    ];

    private array $roles = [];

    private Permission $canBeAuthorPermission;

    public function __construct(
        protected ImagePackage $imagePackage,
        protected ContainerInterface $container,
        EntityManagerInterface $entityManager,
        string $name = null
    )
    {
        parent::__construct($container, $entityManager, $name);
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        $this->roles                 = $this->getRoles();
        $this->canBeAuthorPermission = $this->getPermission('canBeAuthor');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $oldUsers   = $this->getUsers();
        $totalCount = count($oldUsers);

        foreach ($oldUsers as $i => $oldUser) {
            if (!$this->userByEmailExists($oldUser['email'])) {
                $this->createUser($oldUser, $this->getTranslations($oldUser));
            }
//            else {
//                /** @var User $user */
//                $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $oldUser['email']]);
//                $this->updateUserRoles($user, $oldUser);
//
//                $this->entityManager->persist($user);
//                $this->entityManager->flush();
//            }

            $this->renderStatus($i + 1, $totalCount);
        }

        return Command::SUCCESS;
    }

    private function getUsers(): array
    {
        return $this->oldEntityManager->getConnection()->executeQuery("SELECT u.* 
                FROM rp_users u
                WHERE (uid IS NULL) OR (`type` = 'reporter' AND uid IS NOT NULL )
                ")->fetchAllAssociative();
    }

    private function getTranslations(array $user): array
    {
        $records = $this->oldEntityManager->getConnection()->executeQuery("SELECT * 
                FROM rp_users_translation ut
                WHERE ut.user_id = :user
                ", ['user' => $user['id']])
            ->fetchAllAssociative();

        $translations = [];
        foreach ($records as $record) {
            $translations[$record['lang']] = [
                'first_name' => $record['first_name'],
                'last_name'  => $record['last_name'],
                'lang'       => $record['lang']
            ];
        }

        foreach (['ge', 'ru', 'en'] as $language) {
            if (!isset($translations[$language])) {
                $translations[$language] = [
                    'first_name' => $user['first_name'],
                    'last_name'  => $user['last_name'],
                    'lang'       => $language
                ];
            }
        }

        return array_values($translations);
    }

    private function userByEmailExists(string $email): bool
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);
        return !empty($userRepository->findOneBy(['email' => $email]));
    }

    private function createUser(array $oldUser, array $translations = [])
    {
        $user = new User();
        $user
            ->setStatus(new Status((int)$oldUser['status'] === 1 ? 1 : 0))
            ->setEmail($oldUser['email'])
            ->setPassword($oldUser['password']);

        foreach ($translations as $oldTranslation) {
            $translation = new Translation();

            $translation
                ->setLanguage($oldTranslation['lang'])
                ->setFirstName($oldTranslation['first_name'])
                ->setLastName($oldTranslation['last_name'])
                ->setSlug(Slugify::create()->slugify($oldTranslation['first_name'] . '_' . $oldTranslation['last_name']));

            $user->addTranslation($translation);
        }

        $this->updateUserRoles($user, $oldUser);

        $photo = $this->savePhoto($oldUser);
        $this->copyPhoto((int)$oldUser['id'], $photo->getFilename());

        $user->setPhoto($photo);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->createConformity($oldUser['id'], $user->getId(), 'user');
    }

    private function updateUserRoles(User $user, array $oldUser)
    {
        $roles       = ($user->getEmail() == 'root@report.ge')
            ? [$this->roles['SuperAdministrator']]
            : [$this->roles[self::ROLES[$oldUser['role_id']]]];
        $permissions = [];

        if ($oldUser['type'] == 'reporter') {
            $permissions[] = $this->canBeAuthorPermission;
        }

        $userCommand = new UserCommand($roles, $permissions);
        $user->sync('roles', $userCommand->getRoles(true));
        if ($permissions) {
            $user->sync('permissions', $userCommand->getPermissions(true));
        }
    }

    private function getRoles(): array
    {
        /** @var Role[] $roles */
        $roles = $this->entityManager->getRepository(Role::class)->findAll();
        $items = [];
        foreach ($roles as $role) {
            $items[$role->getName()] = $role;
        }
        return $items;
    }

    private function getPermission(string $name): Permission
    {
        /** @var Permission $permission */
        $permission = $this->entityManager->getRepository(Permission::class)->findOneBy(['name' => $name]);
        if (empty($permission)) {
            throw new \Exception('Permission canBeAuthor not found');
        }
        return $permission;
    }

    private function savePhoto(array $user): Photo
    {
        $photo = new Photo();
        $photo
            ->setFilename($filename = Uuid::v4() . '.jpg')
            ->setOriginalFilename(Slugify::create()->slugify($user['first_name'] . '-' . $user['last_name']))
            ->setCreatedAt(new DateTime());

        foreach (['ge', 'ru', 'en'] as $language) {
            $translation = new PhotoTranslation();
            $translation->setLanguage($language);
            $photo->addTranslation($translation);
        }

        $this->entityManager->persist($photo);
        $this->entityManager->flush();

        return $photo;
    }

    private function copyPhoto(int $userId, string $filename): void
    {
        $photoPath = $this->imagePackage->getAbsolutePath($filename);
        if (!is_file($photoPath)) {
            @copy(
                sprintf('https://report.ge/storage/users/%d/default.jpg', $userId),
                $photoPath
            );
        }
    }
}