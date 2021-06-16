<?php

declare(strict_types=1);

namespace Ria\Bundle\UserBundle\Form\Grid;

use Doctrine\ORM\EntityManagerInterface;
use Ria\Bundle\DataGridBundle\Grid\Grid;
use Ria\Bundle\DataGridBundle\Grid\GridConfig;
use Ria\Bundle\DataGridBundle\Grid\GridManager;
use Ria\Bundle\UserBundle\Repository\RoleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class RoleGrid
{
    private ?Request $request;

    public function __construct(
        private RoleRepository $roleRepository,
        private GridManager $gridManager,
        private EntityManagerInterface $entityManager,
        RequestStack $requestStack,
    ) {
        $this->request = $requestStack->getMasterRequest();
    }

    public function createView(): Grid
    {
        $queryBuilder = $this->roleRepository
            ->createQueryBuilder('r')
            ->orderBy('r.id', 'DESC');

        $gridConfig = (new GridConfig())
            ->setQueryBuilder($queryBuilder)
            ->setCountFieldName('r.id')
            ->addField('r.id', [
                'label' => 'Id',
                'sortable' => true,
                'filterable' => true,
            ])
            ->addField('r.name', [
                'label' => 'Name',
                'filterable' => true,
            ]);

        return $this->gridManager->getGrid($gridConfig, $this->request);
    }
}