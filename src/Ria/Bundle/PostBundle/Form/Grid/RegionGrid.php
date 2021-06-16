<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Form\Grid;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Ria\Bundle\DataGridBundle\Grid\Grid;
use Ria\Bundle\DataGridBundle\Grid\GridConfig;
use Ria\Bundle\DataGridBundle\Grid\GridManager;
use Ria\Bundle\PostBundle\Query\Repository\RegionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class RegionGrid
{
    private ?Request $request;

    public function __construct(
        private RegionRepository $regionRepository,
        private GridManager $gridManager,
        private EntityManagerInterface $entityManager,
        RequestStack $requestStack,
    ) {
        $this->request = $requestStack->getMasterRequest();
    }

    public function createView(): Grid
    {
        $queryBuilder = $this->regionRepository
            ->createQueryBuilder('r')
            ->select('r', 'rt')
            ->join('r.translations', 'rt', Join::WITH, 'rt.language = :language')
            ->orderBy('r.id', 'DESC')
            ->setParameter('language', $this->request->getLocale());

        $gridConfig = (new GridConfig())
            ->setQueryBuilder($queryBuilder)
            ->setCountFieldName('r.id')
            ->addField('r.id', [
                'label' => 'Id',
                'sortable' => true,
                'filterable' => true,
            ])
            ->addField('sorting', [
                'label' => 'Order',
                'uniqueId' => 'order',
            ])
            ->addField('rt.title', [
                'label' => 'Title',
                'filterable' => true,
            ]);

        return $this->gridManager->getGrid($gridConfig, $this->request);
    }
}