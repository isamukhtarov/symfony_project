<?php

declare(strict_types=1);

namespace Ria\Bundle\VoteBundle\Form\Grid;

use Doctrine\ORM\EntityManagerInterface;
use Ria\Bundle\DataGridBundle\Grid\Grid;
use Ria\Bundle\DataGridBundle\Grid\GridConfig;
use Ria\Bundle\DataGridBundle\Grid\GridManager;
use Ria\Bundle\VoteBundle\Repository\VoteRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class VoteGrid
{
    private ?Request $request;

    public function __construct(
        private VoteRepository $votesRepository,
        private GridManager $gridManager,
        private EntityManagerInterface $entityManager,
        private ParameterBagInterface $parameterBag,
        RequestStack $requestStack,
    ) {
        $this->request = $requestStack->getMasterRequest();
    }

    public function createView(): Grid
    {
        $lang = $this->request->query->get('filter_v_language') ?
                $this->request->query->get('filter_v_language') : $this->parameterBag->get('app.locale');

        $queryBuilder = $this->votesRepository
            ->createQueryBuilder('v')
            ->where('v.language = :lang')
            ->setParameter('lang', $lang)
            ->orderBy('v.id', 'DESC');

        $gridConfig = (new GridConfig())
            ->setQueryBuilder($queryBuilder)
            ->setCountFieldName('v.id')
            ->addField('v.id', [
                'label' => 'Id',
                'sortable' => true,
            ])
            ->addField('v.title', [
                'label' => 'form.first_name',
                'filterable' => true,
            ])
            ->addField('v.status', [
                'label' => 'Status',
                'uniqueId' => 'status',
                'autoEscape' => false,
                'filterable' => true,
            ]);

        return $this->gridManager->getGrid($gridConfig, $this->request);
    }

}