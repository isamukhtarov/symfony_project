<?php
declare(strict_types=1);

namespace Ria\Bundle\CoreBundle\Component\ViewsCounter;

use Doctrine\ORM\EntityManagerInterface;
use Ria\Bundle\CoreBundle\Component\ViewsCounter\Collector\Collector;

class PostsViewsProcessorService
{
    /**
     * @var Collector[]
     */
    private array $collectors = [];

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function addCollector(Collector $collector): void
    {
        $this->collectors[] = $collector;
    }

    public function process(): void
    {
        foreach ($this->collectors as $collector) {
            $this->updateViews($collector->collect());
        }
    }

    public function updateViews(array $viewsCollection): void
    {
        foreach ($viewsCollection as $postIdAndLang => $viewsCount) {
            [$postId, $lang] = explode('_', $postIdAndLang);

            $successful = (bool)$this->entityManager->createQuery(/** @lang sql */ <<<SQL
                    UPDATE Ria\Bundle\PostBundle\Entity\Post\Post p
                    SET p.views = p.views + :views
                    WHERE p.id = :id
SQL)
                ->execute(['views' => $viewsCount, 'id' => $postId]);

            if ($successful) {
                $this->entityManager->getConnection()->insert('post_views', [
                    'post_id' => $postId,
                    'views'   => $viewsCount
                ]);
            }
        }
    }

}