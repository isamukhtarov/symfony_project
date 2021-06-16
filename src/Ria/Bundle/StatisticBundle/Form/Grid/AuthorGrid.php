<?php

declare(strict_types=1);

namespace Ria\Bundle\StatisticBundle\Form\Grid;

use DateTime;
use Doctrine\ORM\QueryBuilder;
use Ria\Bundle\DataGridBundle\Grid\Grid;
use Ria\Bundle\DataGridBundle\Grid\GridConfig;
use Ria\Bundle\DataGridBundle\Grid\GridManager;
use Ria\Bundle\StatisticBundle\Component\Filters;
use Ria\Bundle\StatisticBundle\Component\StatisticGridInterface;
use Ria\Bundle\UserBundle\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

class AuthorGrid extends Filters implements StatisticGridInterface
{

    private string $locale;

    public function __construct(
        private GridManager $gridManager,
        private UserRepository $userRepository,
        TranslatorInterface $translation,
        RequestStack $requestStack
    ){
        parent::__construct($translation, $requestStack);
        $this->locale = $this->request->getLocale();
    }

    public function createView(): Grid
    {
        $queryBuilder = $this->getQueryBuilder($this->request->getLocale());

        $gridConfig = new GridConfig();

        $gridConfig
            ->setQueryBuilder($queryBuilder)
            ->setCountFieldName('u.id')
            ->addField('author_name', [
                'label' => 'statistic.Author',
            ])
            ->addField('post_views', [
                'label' => 'statistic.Post Views',
                'uniqueId' => 'views',
                'sortable' => true
            ])
            ->addField('post_count', [
                'label' => 'statistic.Post Count',
                'sortable' => true
            ]);

        return $this->gridManager->getGrid($gridConfig, $this->request, disablePaginate: true);
    }

    public function getQueryBuilder(string $locale): QueryBuilder
    {
        $authorIds = $this->getAuthorIds();

        $queryBuilder = $this->userRepository->createQueryBuilder('u');
        $queryBuilder
            ->select(['u.id', "CONCAT(ut.firstName, ' ', ut.lastName) AS author_name"])
            ->addSelect("(SELECT SUM(pv.views) FROM Ria\Bundle\PostBundle\Entity\Post\Post p
                                            JOIN Ria\Bundle\PostBundle\Entity\Post\Views pv  
                                             WHERE pv.postId = p.id AND p.author = u.id AND p.isPublished = 1 AND 
                                             ({$this->getAppropriateQuery()})) post_views")
            ->addSelect("(SELECT COUNT(pl.id) FROM Ria\Bundle\PostBundle\Entity\Post\Post pl
                                             WHERE pl.author = u.id AND pl.isPublished = 1 AND ({$this->getAppropriateQueryForPostCount()})) post_count")
            ->join('u.translations', 'ut', 'WITH', 'ut.language = :language')
            ->where('u.status.status = :status')
            ->andWhere($queryBuilder->expr()->in('u.id', $authorIds))
            ->groupBy('author_name')
            ->setParameters([
                'status' => true,
                'language' => $locale
            ]);

        foreach ($this->getAppropriatesParams() as $param => $value) {
            $queryBuilder->setParameter($param, $value);
        }

        return $queryBuilder;
    }

    public function getAuthorIds(): array
    {
        $authors = $this->userRepository->getByPermission('canBeAuthor');

        return array_map(function ($author) {
            return $author->getId();
        }, $authors);
    }
}