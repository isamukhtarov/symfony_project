<?php

namespace Ria\Bundle\CoreBundle\Query;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Happyr\DoctrineSpecification\Filter\Filter;
use Happyr\DoctrineSpecification\Logic\AndX;
use Happyr\DoctrineSpecification\Query\QueryModifier;
use Happyr\DoctrineSpecification\Result\ResultModifier;
use ReflectionClass;
use ReflectionException;

trait EntitySpecificationRepositoryTrait
{
    use \Happyr\DoctrineSpecification\EntitySpecificationRepositoryTrait;

    abstract function getSpecsNamespace(): string;

    public function getHydrationMode(): ?string
    {
        return null;
    }

    public function match(Filter|QueryModifier|array $specification, ?string $hydrationMode = null, ResultModifier $modifier = null): mixed
    {
        if (is_array($specification)) {
            $specification = $this->getSpecifications($specification);
        }

        $query = $this->getQuery($specification, $modifier);

        return $query->getResult($hydrationMode ?? $this->getHydrationMode());
    }

    /**
     * Get single result when you match with a Specification.
     *
     * @param Filter|QueryModifier $specification
     * @param string|int|null $hydrationMode Processing mode to be used during the hydration process.
     * @param ResultModifier|null $modifier
     *
     * @return mixed
     * @throws ReflectionException
     * @throw Exception\NonUniqueException  If more than one result is found
     * @throw Exception\NoResultException   If no results found
     *
     */
    public function matchSingleResult($specification, $hydrationMode = null, ResultModifier $modifier = null)
    {
        if (is_array($specification)) {
            $specification = $this->getSpecifications($specification);
        }

        $query = $this->getQuery($specification, $modifier);

        try {
            return $query->getSingleResult($hydrationMode ?? $this->getHydrationMode());
        } catch (NonUniqueResultException $e) {
            throw new \Happyr\DoctrineSpecification\Exception\NonUniqueResultException($e->getMessage(), $e->getCode(), $e);
        } catch (NoResultException $e) {
            throw new \Happyr\DoctrineSpecification\Exception\NoResultException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Get single result or null when you match with a Specification.
     *
     * @param Filter|QueryModifier $specification
     * @param string|int|null $hydrationMode Processing mode to be used during the hydration process.
     * @param ResultModifier|null $modifier
     *
     * @return mixed|null
     * @throws ReflectionException
     * @throw Exception\NonUniqueException  If more than one result is found
     *
     */
    public function matchOneOrNullResult($specification, $hydrationMode = null, ResultModifier $modifier = null)
    {
        try {
            if (is_array($specification)) {
                $specification = $this->getSpecifications($specification);
            }

            return $this->matchSingleResult($specification, $hydrationMode ?? $this->getHydrationMode(), $modifier);
        } catch (\Happyr\DoctrineSpecification\Exception\NoResultException $e) {
            return null;
        }
    }

    /**
     * @param array $specifications
     * @return AndX|object
     * @throws ReflectionException
     */
    public function getSpecifications(array $specifications): AndX
    {
        $specs = [];

        foreach (array_filter($specifications) as $index => $value) {
            $specs[] = $this->getFilterClass($index, $value);
        }

        return (new ReflectionClass(AndX::class))->newInstanceArgs($specs);
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array $filters
     * @throws ReflectionException
     */
    public function applySpecifications(QueryBuilder $queryBuilder, array $filters): void
    {
        $specs = [];

        $this->setAlias($queryBuilder->getRootAliases()[0]);

        foreach (array_filter($filters) as $index => $value) {
            $specs[] = $this->getFilterClass($index, $value);
        }

        $this->applySpecification($queryBuilder, (new ReflectionClass(AndX::class))->newInstanceArgs($specs));
    }

    private function getFilterClass(string $index, string|array $value)
    {
        if (is_array($value)) {
            if ($this->isAssoctive($value)) {
                $filterName      = array_key_first($value);
                $filterArguments = [$value[$filterName]];
            } else {
                $filterName      = $index;
                $filterArguments = [$value];
            }
        } else {
            if (is_string($value)) {
                $filterName = $value;
                $filterArguments = [];
            } else {
                $filterName = $index;
                $filterArguments = [$value];
            }
        }

        $class_name = $this->getSpecsNamespace() . '\\' . ucfirst($filterName);

        $dependencies = [];
        $constructor  = (new ReflectionClass($class_name))->getConstructor();

        if ($constructor !== null) {
            foreach ($constructor->getParameters() as $i => $param) {
                if ($param->isVariadic()) {
                    break;
                } elseif ($param->isDefaultValueAvailable()) {
                    if (!isset($filterArguments[$i])) {
                        $dependencies[] = $param->getDefaultValue();
                    }
                }
            }
        }

        $dependencies[array_key_last($dependencies)] = $this->getAlias();
        $arguments = array_merge($filterArguments, array_values($dependencies));

        return (new ReflectionClass($class_name))->newInstanceArgs($arguments);
    }

    protected function isAssoctive(array $input): bool
    {
        if ([] === $input) return false;
        return array_keys($input) !== range(0, count($input) - 1);
    }
}