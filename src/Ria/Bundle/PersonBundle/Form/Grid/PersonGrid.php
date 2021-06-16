<?php
declare(strict_types=1);

namespace Ria\Bundle\PersonBundle\Form\Grid;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Ria\Bundle\DataGridBundle\Grid\Grid;
use Ria\Bundle\DataGridBundle\Grid\GridConfig;
use Ria\Bundle\DataGridBundle\Grid\GridManager;
use Ria\Bundle\PersonBundle\Entity\Person\Type;
use Ria\Bundle\PersonBundle\Query\Repositories\PersonRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;


class PersonGrid
{
    private ?Request $request;

    public function __construct(
        private PersonRepository $personRepository,
        private GridManager $gridManager,
        private EntityManagerInterface $entityManager,
        RequestStack $requestStack,
    ) {
        $this->request = $requestStack->getMasterRequest();
    }

    public function createView(): Grid
    {
        $queryBuilder = $this->personRepository
            ->createQueryBuilder('p')
            ->select('p', 'pt')
            ->join('p.translations', 'pt', Join::WITH, 'pt.language = :language')
            ->where('p.type.type = :type')
            ->orderBy('p.id', 'DESC')
            ->setParameter('type', Type::PERSON)
            ->setParameter('language', $this->request->getLocale());

        $gridConfig = (new GridConfig())
            ->setQueryBuilder($queryBuilder)
            ->setCountFieldName('p.id')
            ->addField('p.id', [
                'label' => 'Id',
                'sortable' => true,
                'filterable' => true,
            ])
            ->addField('pt.first_name', [
                'label' => 'first_name',
                'filterable' => true,
            ])
            ->addField('pt.last_name', [
                'label' => 'last_name',
                'filterable' => true,
            ])
            ->addField('pt.position', [
                'label' => 'position',
                'filterable' => true,
            ])
            ->addField('p.status', [
                'label' => 'Status',
                'uniqueId' => 'status',
                'autoEscape' => false,
                'filterable' => true,
            ]);

        return $this->gridManager->getGrid($gridConfig, $this->request);
    }

}