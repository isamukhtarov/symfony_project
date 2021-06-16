<?php
declare(strict_types=1);

namespace Ria\Bundle\DataGridBundle\Grid;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Ria\Bundle\DataGridBundle\Grid\ItemListNormalizer\NormalizerInterface;
use Ria\Bundle\DataGridBundle\Tool\{RequestParamsTool, UrlTool};
use Ria\Bundle\DataGridBundle\Paginator\{PaginatorManager, PaginatorConfig};

class GridManager
{
    public function __construct(
        protected PaginatorManager $paginatorManager,
        protected NormalizerInterface $itemListNormalizer,
        protected string $hydratorClass
    ) {}

    public function getGrid(GridConfig $gridConfig, Request $request, Grid $grid = null, bool $disablePaginate = false): Grid
    {
        $queryBuilder = $gridConfig->getQueryBuilder();

        // create grid objet
        if ($grid === null) {
            $grid = new Grid();
        }

        $grid->setGridConfig($gridConfig)
            ->setUrlTool(new UrlTool())
            ->setRequestUri($request->getRequestUri())
            ->setRequestCurrentRoute($request->attributes->get("_route"))
            ->setRequestCurrentRouteParams($request->attributes->get("_route_params"));

        // create base request
        $gridQueryBuilder = clone($queryBuilder);

        // Apply filters
        $filters = $this->getFiltersFromQueryString($request->getQueryString());
        $this->applyFilters($gridQueryBuilder, $filters);

        // Apply selector
        $selectorField = $request->query->get($grid->getSelectorFieldFormName(),"");
        $selectorValue = $request->query->get($grid->getSelectorValueFormName(),"");
        $this->applySelector($gridQueryBuilder, $grid, $selectorField, $selectorValue);

        // Apply sorting
        $sortField = $request->query->get($grid->getSortFieldFormName(),"");
        $sortOrder = $request->query->get($grid->getSortOrderFormName(),"");
        $this->applySort($gridQueryBuilder, $grid, $sortField, $sortOrder);

        // build paginator
        if (!$disablePaginate) {
            $paginatorConfig = $gridConfig->getPaginatorConfig();
            if ($paginatorConfig === null) {
                $paginatorConfig = new PaginatorConfig();
                $paginatorConfig->setCountFieldName($gridConfig->getCountFieldName());
                $paginatorConfig->setName($gridConfig->getName());
                $paginatorConfig->setQueryBuilder($gridQueryBuilder);
            }
            if (is_null($paginatorConfig->getQueryBuilder())) {
                $paginatorConfig->setQueryBuilder($gridQueryBuilder);
            }
            $paginator = $this->paginatorManager->getPaginator($paginatorConfig, $request);
            $grid->setPaginator($paginator);

            // calculate limits
            $gridQueryBuilder->setMaxResults($paginator->getPaginatorConfig()->getItemCountInPage());
            $gridQueryBuilder->setFirstResult(($paginator->getCurrentPage()-1) * $paginator->getPaginatorConfig()->getItemCountInPage());
        }
        // execute request
        $query = $gridQueryBuilder->getQuery();

        $normalizedItemList = $this->itemListNormalizer->normalize($query, $gridQueryBuilder, $this->hydratorClass);
        $grid->setItemList($normalizedItemList);

        return $grid;
    }

    protected function getFiltersFromQueryString(?string $queryString): array
    {
        if (!$queryString) {
            return [];
        }

        $queryParams = RequestParamsTool::parseQueryString($queryString);

        $filters = array_filter($queryParams, function ($item) {
            return str_contains($item, 'filter_');
        }, ARRAY_FILTER_USE_KEY);

        $filters = array_filter($filters, "strlen");

        return $filters;
    }

    protected function applyFilters(QueryBuilder $queryBuilder, array $filters): void
    {
        foreach ($filters as $filter => $value) {
            $columnName = str_replace('filter_', '', $filter);
            $param = str_replace('.', '_', $columnName);

            $queryBuilder->andWhere(
                $queryBuilder->expr()->like($columnName, ':' . $param)
            )
            ->setParameter($param, '%' . $value . '%');
        }
    }

    protected function applyFilter(QueryBuilder $queryBuilder, Grid $grid, $filter): void
    {
        if (!$filter) {
            return;
        }
        $fieldList = $grid->getGridConfig()->getFieldList();
        $filterRequestList = array();
        foreach ($fieldList as $field) {
            if ($field->getFilterable()) {
                $filterRequestList[] = $queryBuilder->expr()->like($field->getFieldName(), ":filter");
            }
        }
        if (count($filterRequestList) > 0) {
            $reflectionMethod = new \ReflectionMethod($queryBuilder->expr(), "orx");
            $queryBuilder->andWhere($reflectionMethod->invokeArgs($queryBuilder->expr(), $filterRequestList));
            $queryBuilder->setParameter("filter", "%".$filter."%");
        }
        $grid->setFilterValue($filter);
    }

    protected function applySelector(QueryBuilder $queryBuilder, Grid $grid, $selectorField, $selectorValue): void
    {
        if (!$selectorField) {
            return;
        }
        $queryBuilder->andWhere($selectorField." = :selectorValue");
        $queryBuilder->setParameter("selectorValue", $selectorValue);

        $grid->setSelectorField($selectorField);
        $grid->setSelectorValue($selectorValue);
    }

    protected function applySort(QueryBuilder $gridQueryBuilder, Grid $grid, $sortField, $sortOrder): void
    {
        if (!$sortField) {
            return;
        }

        $sortFieldObject = null;
        $fieldList = $grid->getGridConfig()->getFieldList();
        foreach ($fieldList as $field) {
            if ($field->getFieldName() == $sortField) {
                if ($field->getSortable() === true) {
                    $sortFieldObject = $field;
                    break;
                }
            }
        }
        if (!$sortFieldObject) {
            return;
        }
        if ($sortOrder != "DESC") {
            $sortOrder = "ASC";
        }
        $gridQueryBuilder->orderBy($sortField, $sortOrder);
        $grid->setSortField($sortField);
        $grid->setSortOrder($sortOrder);
    }

}
