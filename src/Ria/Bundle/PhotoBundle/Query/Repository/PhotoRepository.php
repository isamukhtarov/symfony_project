<?php
declare(strict_types=1);

namespace Ria\Bundle\PhotoBundle\Query\Repository;

use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Ria\Bundle\CoreBundle\Repository\AbstractRepository;
use Ria\Bundle\PhotoBundle\Entity\Photo;
use Ria\Bundle\PhotoBundle\Query\Hydrator\PhotoHydrator;
use Ria\Bundle\PhotoBundle\Query\ViewModel\PhotoViewModel;

/**
 * @method Photo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Photo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Photo[]    findAll()
 * @method Photo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Photo[]    findById(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhotoRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Photo::class);
    }

    public function list(?string $q = null, ?string $lastTimestamp = null)
    {
        $query = $this->createQueryBuilder('p')
            ->orderBy('p.created_at', 'desc')
            ->setMaxResults(20);
        if ($q) {
            $query->where($query->expr()->like('p.original_filename', ':q'))
                  ->setParameter(':q', '%' . $q . '%');
        }

        if ($lastTimestamp) {
            $query
                ->andWhere('p.created_at < :timestamp')
                ->setParameter('timestamp', $lastTimestamp);
        }
        return $query
            ->getQuery()
            ->getResult();
    }

    public function latestOne()
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.id', 'desc')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param int $limit
     * @return int|mixed|string
     */
    public function limited(int $limit)
    {
        return $this->createQueryBuilder('p')
            ->setMaxResults($limit)
            ->orderBy('p.id', 'desc')
            ->getQuery()
            ->execute();
    }

    /**
     * @param string $filename
     * @param string $language
     * @return PhotoViewModel|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getPhotoInfo(string $filename, string $language): ?PhotoViewModel
    {
        return $this->createQueryBuilder('p')
            ->select(['p.id', 'p.filename', 'pt.information', 'pt.author'])
            ->innerJoin('p.translations', 'pt', 'WITH', 'pt.language = :lang')
            ->where('p.filename = :filename')
            ->setParameters(['lang' => $language, 'filename' => $filename])
            ->getQuery()
            ->getOneOrNullResult(PhotoHydrator::HYDRATION_MODE);
    }

    public function getGallery(string $language, int $postId, ?int $mainPhotoId): array
    {
        return $this->getEntityManager()->getConnection()->executeQuery(<<<QUERY
            SELECT `p`.`id`, `p`.`filename`, `pl`.`information`, `pl`.`author`
            FROM `photos` `p`
            INNER JOIN `photos_lang` `pl` ON (`pl`.`photo_id` = `p`.`id` AND `pl`.`language` = :language)
            INNER JOIN `post_photo` `pp` ON (`pp`.`photo_id` = `p`.`id`)
            WHERE (`pp`.`post_id` = :post) AND (`p`.`id` <> :main)
            ORDER BY `pp`.sort
QUERY
            , [
                'language' => $language,
                'post'     => $postId,
                'main'     => $mainPhotoId ?? ''
            ]
        )->fetchAllAssociative();
    }

}