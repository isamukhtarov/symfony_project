<?php
declare(strict_types=1);

namespace Ria\Bundle\CoreBundle\Console\Command;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Ria\Bundle\CoreBundle\Entity\Meta;
use Ria\Bundle\PhotoBundle\Entity\Photo;
use Ria\Bundle\PhotoBundle\Entity\Translation as PhotoTranslation;
use Ria\Bundle\PhotoBundle\Query\Repository\PhotoRepository;
use Ria\Bundle\PhotoBundle\Service\ImagePackage;
use Ria\Bundle\PostBundle\Entity\Category\Category;
use Ria\Bundle\PostBundle\Entity\Post\Post;
use Ria\Bundle\PostBundle\Entity\Post\PostPhoto;
use Ria\Bundle\PostBundle\Entity\Post\Status;
use Ria\Bundle\PostBundle\Entity\Post\Type;
use Ria\Bundle\PostBundle\Entity\Widget\Type as WidgetType;
use Ria\Bundle\PostBundle\Entity\Widget\Widget;
use Ria\Bundle\UserBundle\Entity\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class MigratePostsCommand extends OldDataMigrationCommand
{

    private const LIMIT = 1500;

    private const STATUSES = [
        1 => 'read',
        4 => 'waiting_for_correction',
        5 => 'waiting_for_correction',
        0 => 'on_moderation',
        6 => 'archived',
        2 => 'deleted',
        7 => 'private',
        9 => 'deleted',
    ];

    private const TYPES = [
        1 => Type::POST,
        3 => Type::PHOTO,
        2 => Type::VIDEO
    ];

    private const POSITIONS = [
        2 => 'top',
        5 => 'export',
        9 => 'editor_choice'
    ];

    private array $oldConformityIds = [];

    public function __construct(
        protected TranslatorInterface $translator,
        protected ImagePackage $imagePackage,
        protected ContainerInterface $container,
        EntityManagerInterface $entityManager,
        string $name = null
    )
    {
        parent::__construct($container, $entityManager, $name);
    }

    protected function configure()
    {
        $this->addArgument(
            'year', InputArgument::REQUIRED, 'Year to parse'
        );
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $records = $this->entityManager
            ->getConnection()
            ->fetchAllAssociative('SELECT * FROM `old_conformity`');

        foreach ($records as $record) {
            $this->oldConformityIds[$record['type']][$record['old_id']] = $record['current_id'];
        }

        parent::initialize($input, $output);

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $year = $input->getArgument('year');

        if (empty($year) || !in_array($year, range(2019, 2021))) {
            throw new \InvalidArgumentException('Invalid year.');
        }

        $lastPostTimeFile = __DIR__ . "/last_post_time_{$year}.txt";
        $lastPostDate     = is_file($lastPostTimeFile) ? file_get_contents($lastPostTimeFile) : date("{$year}-01-01 00:00:00");

        $oldPostsStatement = $this->getPosts($lastPostDate, date("{$year}-12-31 11:59:59"));

        $i = 0;
        while ($oldPost = $oldPostsStatement->fetchAssociative()) {
            if ($this->postExist($oldPost['url'], $oldPost['lang'])) {
                if (strtotime($oldPost['created']) < time()) {
                    file_put_contents($lastPostTimeFile, $oldPost['created']);
                }
                continue;
            }

            $createdPostGroup = $this->createPost($oldPost, null, $lastPostTimeFile);

            $otherTranslations = $this->getOtherTranslations($oldPost['group'], $oldPost['lang']);
            foreach ($otherTranslations as $oldTranslation) {
                if (!$this->postExist($oldTranslation['url'], $oldTranslation['lang'])) {
                    $this->createPost($oldTranslation, $createdPostGroup);

//                    $output->writeln(++$i . ') ' . $oldTranslation['title']);
                }
            }

//            $output->writeln(++$i . ') ' . $oldPost['title']);
        }

        return Command::SUCCESS;
    }

    private function createPost(array $oldPost, int $groupId = null, string $lastPostTimeFile = null): int
    {
        $this->entityManager->getConnection()->beginTransaction();

        try {
            $positions = $this->getPostPositions($oldPost['id']);

            $status = new Status(self::STATUSES[(int)$oldPost['status']]);

            $meta = new Meta(
                $oldPost['meta_title'] ?? '',
                $oldPost['meta_description'] ?? '',
                $oldPost['meta_keywords'] ?? ''
            );

            $post = new Post();
            $post
                ->setTitle($oldPost['title'])
                ->setDescription($oldPost['description'])
                ->setSlug($oldPost['url'])
                ->setLanguage($oldPost['lang'])
                ->setStatus($status)
                ->setIsPublished($status->isActive() && strtotime($oldPost['created']) < time())
                ->setPublishedAt(new DateTime($oldPost['created']))
                ->setAuthor($author = $this->getAuthor((int)$oldPost['user_id']))
                ->setCreatedBy($author->getId())
                ->setCategory($this->getCategory($oldPost['category_id']))
                ->setMeta($meta)
                ->setType($this->getPostType((int)$oldPost['type']))
                ->setIsMain(isset($positions['top']))
                ->setIsActual(isset($positions['editor_choice']))
                ->setIsExclusive(false)
                ->setIsBreaking(false)
                ->setViews((int)$oldPost['views'])
                ->setCreatedAt(new DateTime($oldPost['created']))
                ->setUpdatedAt(new DateTime($oldPost['updated']));

            $content = $this->saveContentWidgets($oldPost['content'], $post);

            $post->setContent($content);

            if ($groupId) {
                $post->setParent($this->entityManager->getReference(Post::class, $groupId));
            }

            $this->savePhotos($oldPost, $post);

            $this->entityManager->persist($post);
            $this->entityManager->flush();

            if (!empty($lastPostTimeFile) && strtotime($oldPost['created']) < time()) {
                file_put_contents($lastPostTimeFile, $oldPost['created']);
            }

            $this->entityManager->getConnection()->commit();

            // set parent_id
            if (empty($post->getParent())) {
                $post->setParent($post);

                $this->entityManager->persist($post);
                $this->entityManager->flush();
            }

            return $post->getParent()->getId();
        } catch (Exception $exception) {
            $this->entityManager->getConnection()->rollback();

            dd($exception->getMessage());
        }
    }

    private function savePhotos(array $oldPost, Post $post)
    {
        $mainPhotoUUID = empty($oldPost['image']) ? null : explode('/', $oldPost['image'])[1];

        $oldPhotos = $this->getOldPhotos($oldPost['group']);

        if (!empty($mainPhotoUUID)) {
            // Если у поста фотографий нет, но задана главная фотография,
            // то получаем привязанную фототографию
            if (empty($oldPhotos)) {
                $oldPhotos[] = $this->getOldPhoto($mainPhotoUUID);
            } else {
                // проверяем, есть ли главная фотка в прикрепленных фотках новости
                foreach ($oldPhotos as $oldPhoto) {
                    if ($oldPhoto['id'] == $mainPhotoUUID) {
                        $oldPhotos[] = $this->getOldPhoto($mainPhotoUUID);
                        break;
                    }
                }
            }
        }

        $oldPhotos = array_filter($oldPhotos);

        $photos = [];
        foreach ($oldPhotos as $oldPhoto) {
            $photo = $this->getPhoto($oldPhoto['id'] . '.jpg');
            if (empty($photo)) {
                $photo = $this->savePhoto($oldPhoto);
            }

            if ($oldPhoto['id'] == $mainPhotoUUID) {
                $post->setPhoto($photo);
            }

            $this->copyPhoto($oldPhoto['post_group'], $oldPhoto['id']);

            $photos[] = $photo;
        }

        $post->sync('photoRelation', $this->preparePhotos($photos, $post));
    }

    private function getOldPhoto(string $id): array|false
    {
        return $this->oldEntityManager
            ->getConnection()
            ->fetchAssociative('SELECT * FROM rp_photos WHERE id = :id', ['id' => $id]);
    }

    private function getPhoto(string $filename): ?Photo
    {
        /** @var PhotoRepository $photoRepository */
        $photoRepository = $this->entityManager->getRepository(Photo::class);
        /** @var Photo $photo */
        $photo = $photoRepository->findOneBy(['filename' => $filename]);
        return $photo;
    }

    private function getOldPhotos(string $postGroup): array
    {
        return $this->oldEntityManager
            ->getConnection()
            ->fetchAllAssociative('SELECT * FROM rp_photos WHERE post_group = :group', ['group' => $postGroup]);
    }

    private function savePhoto(array $oldPhoto): Photo
    {
        $information = json_decode($oldPhoto['info'], true);
        $author      = json_decode($oldPhoto['source'], true);

        $photo = new Photo();
        $photo
            ->setFilename($oldPhoto['id'] . '.jpg')
            ->setOriginalFilename($oldPhoto['original_name'] ?? '')
            ->setCreatedAt(new DateTime($oldPhoto['created']));

        foreach (['ge', 'ru', 'en'] as $language) {
            $translation = new PhotoTranslation();
            $translation->setLanguage($language);
            $translation->setInformation($information[$language] ?? '');
            $translation->setAuthor($author[$language] ?? '');
            $translation->setSource($oldPhoto['source_link']);

            $photo->addTranslation($translation);
        }

        $this->entityManager->persist($photo);
        $this->entityManager->flush();

        return $photo;
    }

    private function copyPhoto(string $postGroup, string $photoHash): void
    {
        $photoPath = $this->imagePackage->getAbsolutePath($photoHash . '.jpg');
        if (!is_file($photoPath)) {
            $localPhotoPath = sprintf($this->container->get('kernel')->getProjectDir() . '/storage/news/%s/%s.jpg', $postGroup, $photoHash);

            if (is_file($localPhotoPath)) {
                copy(
                    $localPhotoPath,
                    $photoPath
                );
            } else {
                @copy(
                    sprintf('https://report.ge/storage/news/%s/%s.jpg', $postGroup, $photoHash),
                    $photoPath
                );
            }
        }
    }

    private function preparePhotos(array $photos, Post $post): ArrayCollection
    {
        $photoRelations = new ArrayCollection();

        /**
         * @var int $i
         * @var Photo $photo
         */
        foreach ($photos as $i => $photo) {
            $photoRelation = $post->getPhotoRelation()
                ->filter(fn (PostPhoto $postPhoto) => $postPhoto->getPhoto()->getId() == $photo->getId())->first();

            if (!$photoRelation) {
                $photoRelation = (new PostPhoto())
                    ->setPhoto($photo)
                    ->setPost($post);
            }

            $photoRelation->setSort($i + 1);
            $photoRelations->add($photoRelation);
        }

        return $photoRelations;
    }

    private function getOtherTranslations(string $group, string $excludeLanguage): array
    {
        return $this->oldEntityManager->getConnection()->fetchAllAssociative("
                SELECT p.*, pm.title as meta_title, pm.description as meta_description, pm.keywords as meta_keywords
                FROM rp_posts p
                LEFT JOIN rp_post_meta pm ON (pm.post_id = p.id)
                WHERE p.group = :group AND p.lang <> :lang AND p.status <> 3 AND p.category_id IS NOT NULL
            ", ['group' => $group, 'lang' => $excludeLanguage]);
    }

    private function getPostPositions(string $postId): array
    {
        $records = $this->oldEntityManager
            ->getConnection()
            ->fetchAllAssociative('SELECT `type` FROM rp_settings WHERE post_id = :post', ['post' => $postId]);

        $positions = [];
        foreach ($records as $record) {
            if (isset(self::POSITIONS[$record['type']])) {
                $positions[self::POSITIONS[$record['type']]] = true;
            }
        }

        return $positions;
    }

    private function getAuthor(int $oldUserId): User
    {
        /** @var User $author */
        $author = $this->entityManager->getReference(User::class, (int)$this->oldConformityIds['user'][$oldUserId]);
        return $author;
    }

    private function getCategory(string $oldCategoryId): Category
    {
        /** @var Category $category */
        $category = $this->entityManager->getReference(Category::class, $this->oldConformityIds['category'][$oldCategoryId]);
        return $category;
    }

    private function getPostType(int $oldPostType): Type
    {
        return new Type(self::TYPES[$oldPostType] ?? Type::POST, $this->translator);
    }

    private function saveContentWidgets(string $content, Post $post)
    {
        preg_match_all('/{{widget-content-([a-z0-9\-]+)}}/i', $content, $matches);

        foreach ($matches[1] as $i => $widgetId) {
            $oldWidget = $this->getWidget((int)$widgetId);

            if (empty($oldWidget)) {
                $content = str_replace($matches[0][$i], '', $content);
                continue;
            }

            // set first video as main video
            if ($post->getType()->isVideo() && $i === 0 && $oldWidget['type'] == WidgetType::YOUTUBE) {
                // set youtube id
                $post->setYoutubeId(self::getYoutubeHash($oldWidget['content']));

                $content = str_replace($matches[0][$i], '', $content);
                continue;
            }

            $widget = new Widget();
            $widget
                ->setContent($oldWidget['content'])
                ->setType(new WidgetType($oldWidget['type']));
            $this->entityManager->persist($widget);
            $this->entityManager->flush();

            $content = str_replace($matches[0][$i], "{{widget-content-{$widget->getId()}}}", $content);
        }

        return $content;
    }

    private static function getYoutubeHash(string $htmlCode): ?string
    {
        $matches = [];
        if (!preg_match('/\/embed\/([a-z0-9\_\-]+)/i', $htmlCode, $matches)) {
            return null;
        }

        list($matchString, $youtubeHash) = $matches;
        return $youtubeHash;
    }


    private function getWidget(int $id): array|false
    {
        return $this->oldEntityManager->getConnection()
            ->fetchAssociative('SELECT * FROM rp_post_content_widgets WHERE id = :id', [':id' => $id]);
    }

    private function postExist(string $slug, string $language): bool
    {
        return (bool)$this->entityManager->getConnection()->fetchFirstColumn(
            "SELECT COUNT(id) FROM posts WHERE slug = :slug AND language = :lang", [
            'slug' => $slug,
            'lang' => $language
        ])[0];
    }

    private function getPosts(string $dateFrom, string $dateTo)
    {
        $condition = 'p.status <> 3 AND p.category_id IS NOT NULL';
        $params    = [];

        if ($dateFrom) {
            $condition       .= ' AND (p.created > :start) AND (p.created < :end AND p.created < NOW())';
            $params['start'] = $dateFrom;
            $params['end']   = $dateTo;
        }

        return $this->oldEntityManager->getConnection()->executeQuery("
            SELECT p.*,
                   pm.title as meta_title, pm.description as meta_description, pm.keywords as meta_keywords
            FROM rp_posts p
            LEFT JOIN rp_post_meta pm ON (pm.post_id = p.id)
            WHERE {$condition}
            ORDER BY created ASC
            LIMIT " . self::LIMIT . "
        ", $params);
    }

}