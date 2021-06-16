<?php

namespace Ria\Bundle\PostBundle\Form\Grid;

use Doctrine\ORM\Query\Expr\Join;
use Ria\Bundle\DataGridBundle\Grid\Grid;
use Ria\Bundle\DataGridBundle\Grid\GridConfig;
use Ria\Bundle\DataGridBundle\Grid\GridManager;
use Ria\Bundle\PostBundle\Query\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class CategoryGrid
{
    private ?Request $request;

    public function __construct(
        private CategoryRepository $categoryRepository,
        private GridManager $gridManager,
        RequestStack $requestStack,
    ) {
        $this->request = $requestStack->getMasterRequest();
    }

    public function createView(): Grid
    {
        $queryBuilder = $this->categoryRepository
            ->createQueryBuilder('c')
            ->select('c', 'ct')
            ->join('c.translations', 'ct', Join::WITH, 'ct.language = :language')
            ->orderBy('c.id', 'DESC')
            ->setParameter('language', $this->request->getLocale());

        $gridConfig = (new GridConfig())
            ->setQueryBuilder($queryBuilder)
            ->setCountFieldName('c.id')
            ->addField('c.id', [
                'label' => 'Id',
                'sortable' => true,
                'filterable' => true,
            ])
            ->addField('sorting', [
                'label' => 'Order',
                'uniqueId' => 'sorting',
            ])
            ->addField('ct.title', [
                'label' => 'Title',
                'filterable' => true,
            ])
            ->addField('c.status', [
                'label' => 'Status',
                'uniqueId' => 'status',
                'autoEscape' => false,
                'filterable' => true,
            ]);

        return $this->gridManager->getGrid($gridConfig, $this->request);
    }

}