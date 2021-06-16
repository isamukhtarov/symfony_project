<?php

namespace Ria\Bundle\YoutubeBundle\Form\Grid;

use Ria\Bundle\DataGridBundle\Grid\Grid;
use Ria\Bundle\DataGridBundle\Grid\GridConfig;
use Ria\Bundle\DataGridBundle\Grid\GridManager;
use Ria\Bundle\YoutubeBundle\Repository\YoutubeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class YoutubeGrid
{
    private ?Request $request;

    public function __construct(
        private YoutubeRepository $youtubeRepository,
        private GridManager $gridManager,
        RequestStack $requestStack
    ){
        $this->request = $requestStack->getMasterRequest();
    }

    public function createView(): Grid
    {
        $qb = $this->youtubeRepository
            ->createQueryBuilder('y')
            ->select('y')
            ->orderBy('y.id', 'DESC');

        $gridConfig = (new GridConfig())
              ->setQueryBuilder($qb)
              ->setCountFieldName('y.id')
              ->addField('y.id', [
                  'label' => 'Id',
                  'sortable' => true,
                  'filterable' => false
              ])
            ->addField('y.image', [
                'uniqueId' => 'image',
                'label' => 'Image',
                'sortable' => false,
                'filterable' => false
            ])
              ->addField('y.youtubeId', [
                  'label' => 'Youtube Id',
                  'filterable' => true
              ])
            ->addField('y.status', [
                'label' => 'form.status',
                'uniqueId' => 'status',
                'autoEscape' => false,
                'filterable' => true,
            ]);

        return $this->gridManager->getGrid($gridConfig, $this->request);
    }

}