<?php

declare(strict_types=1);

namespace Ria\Bundle\StatisticBundle\Form\Grid;

use Doctrine\ORM\QueryBuilder;
use Ria\Bundle\DataGridBundle\Grid\Grid;
use Ria\Bundle\DataGridBundle\Grid\GridConfig;
use Ria\Bundle\DataGridBundle\Grid\GridManager;
use Ria\Bundle\StatisticBundle\Component\Filters;
use Ria\Bundle\StatisticBundle\Component\StatisticGridInterface;
use Ria\Bundle\UserBundle\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

class TranslatorGrid extends Filters implements StatisticGridInterface
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
            ->addField('translator_name', [
                'label' => 'statistic.Translator',
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
        $translatorIds = $this->getTranslatorIds();

        $queryBuilder = $this->userRepository->createQueryBuilder('u');
        $queryBuilder
            ->select(['u.id', "CONCAT(ut.firstName, ' ', ut.lastName) AS translator_name"])
            ->addSelect("(SELECT SUM(pv.views) FROM Ria\Bundle\PostBundle\Entity\Post\Post p
                                            JOIN Ria\Bundle\PostBundle\Entity\Post\Views pv  
                                             WHERE pv.postId = p.id AND p.translator = u.id AND p.isPublished = 1 AND 
                                             ({$this->getAppropriateQuery()})) post_views")
            ->addSelect("(SELECT COUNT(pl.id) FROM Ria\Bundle\PostBundle\Entity\Post\Post pl
                                             WHERE pl.translator = u.id AND pl.isPublished = 1 AND ({$this->getAppropriateQueryForPostCount()})) post_count")
            ->join('u.translations', 'ut', 'WITH', 'ut.language = :language')
            ->andWhere($queryBuilder->expr()->in('u.id', $translatorIds))
            ->groupBy('translator_name')
            ->setParameter('language', $this->locale);

        foreach ($this->getAppropriatesParams() as $param => $value) {
            $queryBuilder->setParameter($param, $value);
        }

        return $queryBuilder;
    }

    public function getTranslatorIds(): array
    {
        $translators = $this->userRepository->getByRoles(['Translator', 'LeadTranslator']);

        return array_map(function ($translator) {
            return $translator->getId();
        }, $translators);
    }
}