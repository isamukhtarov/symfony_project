<?php

namespace Ria\Bundle\DataGridBundle\Grid;

use Doctrine\ORM\QueryBuilder;
use JetBrains\PhpStorm\Pure;
use Ria\Bundle\DataGridBundle\Paginator\PaginatorConfig;

class GridConfig
{
    protected QueryBuilder|null $queryBuilder = null;
    protected PaginatorConfig|null $paginatorConfig = null;
    protected string $name = 'grid';
    /** @var Field[] */
    protected array $fieldList = [];
    protected array $selectorList = [];
    protected string|null $countFieldName = null;

    public function addField(Field|string $field, $options = [], $tagList = []): self
    {
        if (! (\is_string($field) || $field instanceof Field)) {
            throw new \InvalidArgumentException('Argument $field should be string or instance of DataGridBundle\Grid\Field');
        }

        if (\is_string($field)) {
            $field = new Field($field, $options, $tagList);
        }

        $this->fieldList[] = $field;

        return $this;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setQueryBuilder(QueryBuilder|null $queryBuilder): self
    {
        $this->queryBuilder = $queryBuilder;
        return $this;
    }

    public function getQueryBuilder(): ?QueryBuilder
    {
        return $this->queryBuilder;
    }

    public function setPaginatorConfig(PaginatorConfig $paginatorConfig): self
    {
        $this->paginatorConfig = $paginatorConfig;
        return $this;
    }

    public function getPaginatorConfig(): ?PaginatorConfig
    {
        return $this->paginatorConfig;
    }

    public function setFieldList(array $fieldList): self
    {
        $this->fieldList = $fieldList;
        return $this;
    }

    public function getFieldList(): array
    {
        return $this->fieldList;
    }

    #[Pure] public function getFieldByName(string $name): ?Field
    {
        foreach ($this->fieldList as $field) {
            if ($field->getFieldName() === $name) {
                return $field;
            }
        }

        return null;
    }

    public function getFieldListByTag(string $tag): array
    {
        $matchingFieldList = [];
        foreach ($this->fieldList as $field) {
            if ($field->hasTag($tag)) {
                $matchingFieldList[]  = $field;
            }
        }

        return $matchingFieldList;
    }

    public function setCountFieldName(string $countFieldName): self
    {
        $this->countFieldName = $countFieldName;
        return $this;
    }

    public function getCountFieldName(): string
    {
        return $this->countFieldName;
    }

    public function addSelector(array $selector): self
    {
        $this->selectorList[] = $selector;
        return $this;
    }

    public function getSelectorList(): array
    {
        return $this->selectorList;
    }

    public function setSelectorList(array $selectorList): self
    {
        $this->selectorList = $selectorList;
        return $this;
    }

}
