<?php

declare(strict_types=1);

namespace Ria\Bundle\ConfigBundle\Form\Grid;

use Psr\Container\ContainerInterface;
use Ria\Bundle\DataGridBundle\Grid\Grid;
use Ria\Bundle\DataGridBundle\Grid\GridConfig;
use Ria\Bundle\ConfigBundle\Repository\ConfigRepository;
use Ria\Bundle\DataGridBundle\Grid\GridManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class ConfigGrid
{
    private ?Request $request;

    public function __construct(
        private ConfigRepository $configRepository,
        private ContainerInterface $container,
        private GridManager $gridManager,
        RequestStack $request
    )
    {
        $this->request = $request->getMasterRequest();
    }

    public function createView(): Grid
    {
        $qb = $this->configRepository
            ->createQueryBuilder('c')
            ->select('c')
            ->orderBy('c.id', 'DESC');

        $gridConfig = (new GridConfig())
            ->setQueryBuilder($qb)
            ->setCountFieldName('c.id')
            ->addField('c.id', [
                'label' => 'Id',
                'sortable' => true,
                'filterable' => false
            ])
            ->addField('c.label', [
                'label' => 'Label',
                'filterable' => true
            ])
            ->addField('c.param', [
                'label' => 'Param',
                'filterable' => true
            ])
            ->addField('c.value', [
                'label' => 'Value',
                'uniqueId' => 'value',
                'filterable' => true,
            ]);

        return $this->gridManager->getGrid($gridConfig, $this->request);
    }
}