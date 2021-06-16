<?php

declare(strict_types=1);

namespace Ria\Bundle\UserBundle\Form\Grid;

use Doctrine\ORM\EntityManagerInterface;
use Ria\Bundle\DataGridBundle\Grid\Grid;
use Ria\Bundle\DataGridBundle\Grid\GridConfig;
use Ria\Bundle\DataGridBundle\Grid\GridManager;
use Ria\Bundle\UserBundle\Repository\PermissionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class PermissionGrid
{
    private ?Request $request;

    public function __construct(
        private PermissionRepository $permissionRepository,
        private GridManager $gridManager,
        private EntityManagerInterface $entityManager,
        RequestStack $requestStack,
    ) {
        $this->request = $requestStack->getMasterRequest();
    }

    public function createView(): Grid
    {
        $queryBuilder = $this->permissionRepository
            ->createQueryBuilder('p')
            ->orderBy('p.id', 'DESC');

        $gridConfig = (new GridConfig())
            ->setQueryBuilder($queryBuilder)
            ->setCountFieldName('p.id')
            ->addField('p.id', [
                'label' => 'Id',
                'sortable' => true,
                'filterable' => true,
            ])
            ->addField('p.name', [
                'label' => 'Name',
                'filterable' => true,
            ]);

        return $this->gridManager->getGrid($gridConfig, $this->request);
    }
}