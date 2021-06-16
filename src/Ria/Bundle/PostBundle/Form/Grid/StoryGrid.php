<?php

namespace Ria\Bundle\PostBundle\Form\Grid;

use Doctrine\ORM\Query\Expr\Join;
use Ria\Bundle\DataGridBundle\Grid\GridConfig;
use Ria\Bundle\DataGridBundle\Grid\GridManager;
use Ria\Bundle\PostBundle\Query\Repository\StoryRepository;
use Symfony\Component\HttpFoundation\{RequestStack, Request};

class StoryGrid
{
    private ?Request $request;

    public function __construct(
        private StoryRepository $storyRepository,
        private GridManager $gridManager,
        RequestStack $requestStack,
    ) {
        $this->request = $requestStack->getMasterRequest();
    }

    public function createView()
    {
        $queryBuilder = $this->storyRepository
            ->createQueryBuilder('s')
            ->select('s', 'st')
            ->join('s.translations', 'st', Join::WITH, 'st.language = :language')
            ->orderBy('s.status', 'DESC')
            ->addOrderBy('s.id', 'DESC')
            ->setParameter('language', $this->request->getLocale());

        $gridConfig = (new GridConfig())
            ->setQueryBuilder($queryBuilder)
            ->setCountFieldName('s.id')
            ->addField('s.id', [
                'label' => 'Id',
                'sortable' => true,
                'filterable' => true,
            ])
            ->addField('st.title', [
                'label' => 'Title',
                'filterable' => true,
            ])
            ->addField('s.status', [
                'label' => 'Status',
                'uniqueId' => 'status',
                'autoEscape' => false,
                'filterable' => true,
            ])
            ->addField('s.show_on_site', [
                'label' => 'show_on_site',
                'uniqueId' => 'status',
                'autoEscape' => false,
                'filterable' => true,
            ]);

        return $this->gridManager->getGrid($gridConfig, $this->request);
    }

}