<?php
declare(strict_types=1);

namespace Ria\Bundle\PostBundle\Form\Grid;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Ria\Bundle\DataGridBundle\Grid\Grid;
use Ria\Bundle\DataGridBundle\Grid\GridConfig;
use Ria\Bundle\DataGridBundle\Grid\GridManager;
use Ria\Bundle\PostBundle\Entity\Region\Region;
use Ria\Bundle\PostBundle\Query\Repository\CityRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class CityGrid
{
    private ?Request $request;

    public function __construct(
        private CityRepository $cityRepository,
        private GridManager $gridManager,
        private EntityManagerInterface $entityManager,
        RequestStack $requestStack,
    ) {
        $this->request = $requestStack->getMasterRequest();
    }

    public function createView() : Grid
    {
        $queryBuilder = $this->cityRepository
            ->createQueryBuilder('c')
            ->select('c', 'ct', 'r', 'rt')
            ->join('c.translations', 'ct', Join::WITH, 'ct.language = :language')
            ->join('c.region', 'r')
            ->join('r.translations', 'rt', 'WITH', 'rt.language = :language')
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
            ->addField('ct.title', [
                'label' => 'Title',
                'filterable' => true,
            ])
            ->addField('r.id', [
                'label' => 'Region',
                'formatValueCallback' => fn ($value, $row) => $row['rt.title'],
                'filterable' => true,
                'filterData' => $this->getRegionsList()
            ]);

        return $this->gridManager->getGrid($gridConfig, $this->request);
    }

    private function getRegionsList(): array
    {
        /** @var Region[] $regions */
        $regions = $this->entityManager
            ->getRepository(Region::class)
            ->findAll();

        $result = [];
        foreach ($regions as $region) {
            $result[$region->getId()] = $region->getTranslation($this->request->getLocale())->getTitle();
        }

        return $result;
    }

}