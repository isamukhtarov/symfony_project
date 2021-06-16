<?php

namespace Ria\Bundle\DataGridBundle\Grid;

use JetBrains\PhpStorm\Pure;
use Ria\Bundle\DataGridBundle\DataGridException;
use Ria\Bundle\DataGridBundle\Paginator\Paginator;
use Ria\Bundle\DataGridBundle\Tool\{RequestParamsTool, UrlTool};

class Grid
{
    protected Paginator|null $paginator = null;
    protected GridConfig|null $gridConfig = null;
    protected array $itemList = [];
    protected UrlTool|null $urlTool = null;
    protected string|null $requestUri = null;
    protected string|null $filterValue = null;
    protected string|null $sortField = null;
    protected string|null $sortOrder = null;
    protected bool $debugMode = false;
    protected string|null $selectorField = null;
    protected string|null $selectorValue = null;
    protected string|null $requestCurrentRoute = null;
    protected array $requestCurrentRouteParams = [];

    public function getSelectorUrl(string $selectorField, string $selectorValue): string
    {
        $queryStringToChange = $this->isSelectorSelected($selectorField, $selectorValue)
            ? [$this->getSelectorFieldFormName() => '', $this->getSelectorValueFormName() => '']
            : [$this->getSelectorFieldFormName() => $selectorField, $this->getSelectorValueFormName() => $selectorValue];

        return $this->urlTool->changeRequestQueryString($this->requestUri, $queryStringToChange);
    }

    public function getSortUrl(string $fieldName): string
    {
        $order = $fieldName == $this->getSortField() ? (($this->getSortOrder() === 'ASC') ? 'DESC' : 'ASC') : 'DESC';

        $uri =  $this->urlTool->changeRequestQueryString($this->requestUri, $this->getSortFieldFormName(), $fieldName);
        return $this->urlTool->changeRequestQueryString($uri, $this->getSortOrderFormName(), $order);
    }

    #[Pure] public function getSortCssClass(string $fieldName): string
    {
        $css = '';
        if ($fieldName == $this->getSortField()) {
            $css .= ' kit-grid-sort ';
            $css .= ' kit-grid-sort-'.strtolower($this->getSortOrder()).' ';
        }

        return $css;
    }

    public function displayGridValue(array $row, Field $field): ?string
    {
        $value = null;
        $fieldName = $field->getFieldName();
        if (array_key_exists($fieldName, $row)) {
            $value = $row[$fieldName];
        }

        // real treatment
        if (\is_callable($field->getFormatValueCallback())) {
            $callback = $field->getFormatValueCallback();
            $reflection = new \ReflectionFunction($callback);
            if ($reflection->getNumberOfParameters() === 1) {
                $value =  $callback($value);
            } elseif ($reflection->getNumberOfParameters() === 2) {
                $value =  $callback($value, $row);
            } else {
                throw new DataGridException('Wrong number of parameters in the callback for field '.$field->getFieldName());
            }
        }

            if ($value instanceof \DateTime) {
                $returnValue = $value->format('Y-m-d H:i:s');
            } else {
                $returnValue = $value;
            }

        // auto escape ? (if null, return null, without autoescape...)
        if ($field->getAutoEscape() && $returnValue !== null) {
            $returnValue = htmlspecialchars($returnValue);
        }

        return $returnValue;
    }

    #[Pure] public function getFilterFormName(): string
    {
        return 'kitdg_grid_'.$this->getGridConfig()->getName().'_filter';
    }

    #[Pure] public function getSortFieldFormName(): string
    {
        return 'kitdg_grid_'.$this->getGridConfig()->getName().'_sort_field';
    }

    #[Pure] public function getSortOrderFormName(): string
    {
        return 'kitdg_grid_'.$this->getGridConfig()->getName().'_sort_order';
    }

    #[Pure] public function getSelectorCssSelected($selectorField, $selectorValue): ?string
    {
        if (!$this->isSelectorSelected($selectorField, $selectorValue)) {
            return null;
        }

        return 'kit-grid-selector-selected';
    }

    #[Pure] public function isSelectorSelected($selectorField, $selectorValue): bool
    {
        return $this->getSelectorField() == $selectorField
            && $this->getSelectorValue() == $selectorValue;
    }

    #[Pure] public function getSelectorFieldFormName(): string
    {
        return 'kitdg_grid_'.$this->getGridConfig()->getName().'_selector_field';
    }

    #[Pure] public function getSelectorValueFormName(): string
    {
        return 'kitdg_grid_'.$this->getGridConfig()->getName().'_selector_value';
    }

    #[Pure] public function getGridCssName(): string
    {
        return 'kit-grid-'.$this->getGridConfig()->getName();
    }

    public function setGridConfig(GridConfig $gridConfig): self
    {
        $this->gridConfig = $gridConfig;
        return $this;
    }

    public function getGridConfig(): ?GridConfig
    {
        return $this->gridConfig;
    }

    public function setItemList($itemList): self
    {
        $this->itemList = $itemList;
        return $this;
    }

    public function getItemList(): array
    {
        return $this->itemList;
    }

    public function dump($escape = true): string
    {
        $content = print_r($this->itemList, true);
        if ($escape) {
            $content = htmlspecialchars($content);
        }

        $html = '<pre class="kit-grid-debug">';
        $html .= $content;
        $html .= '</pre>';
        return $html;
    }

    public function setPaginator(Paginator $paginator): self
    {
        $this->paginator = $paginator;
        return $this;
    }

    public function getPaginator(): ?Paginator
    {
        return $this->paginator;
    }

    public function setUrlTool(UrlTool $urlTool): self
    {
        $this->urlTool = $urlTool;
        return $this;
    }

    public function getUrlTool(): ?UrlTool
    {
        return $this->urlTool;
    }

    public function setRequestUri(string $requestUri): self
    {
        $this->requestUri = $requestUri;
        return $this;
    }

    public function getRequestCurrentRoute(): ?string
    {
        return $this->requestCurrentRoute;
    }

    public function setRequestCurrentRoute(string $requestCurrentRoute): self
    {
        $this->requestCurrentRoute = $requestCurrentRoute;
        return $this;
    }

    public function getRequestCurrentRouteParams(): array
    {
        return $this->requestCurrentRouteParams;
    }

    public function setRequestCurrentRouteParams(array $requestCurrentRouteParams): self
    {
        $this->requestCurrentRouteParams = $requestCurrentRouteParams;
        return $this;
    }

    public function getRequestUri(): ?string
    {
        return $this->requestUri;
    }

    public function setFilterValue(string $filterValue)
    {
        $this->filterValue = $filterValue;
    }

    public function getFilterValue(string $fieldName): ?string
    {
        $uri = parse_url($this->getRequestUri(), PHP_URL_QUERY);
        $getParams = RequestParamsTool::parseQueryString($uri);

        return isset($getParams['filter_' . $fieldName]) ? $getParams['filter_' . $fieldName] : null;
    }

    public function setSortField(string $sortField)
    {
        $this->sortField = $sortField;
    }

    public function getSortField(): ?string
    {
        return $this->sortField;
    }

    public function setSortOrder(string $sortOrder)
    {
        $this->sortOrder = $sortOrder;
    }

    public function getSortOrder(): ?string
    {
        return $this->sortOrder;
    }

    public function setSelectorField(string $selectorField)
    {
        $this->selectorField = $selectorField;
    }

    public function getSelectorField(): ?string
    {
        return $this->selectorField;
    }

    public function setSelectorValue(string $selectorValue)
    {
        $this->selectorValue = $selectorValue;
    }

    public function getSelectorValue(): ?string
    {
        return $this->selectorValue;
    }

    public function setDebugMode(bool $debugMode): self
    {
        $this->debugMode = $debugMode;
        return $this;
    }

    public function getDebugMode(): bool
    {
        return $this->debugMode;
    }
}
