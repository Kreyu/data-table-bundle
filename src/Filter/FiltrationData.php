<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;

/**
 * @implements \ArrayAccess<string, FilterData>
 */
class FiltrationData implements \ArrayAccess
{
    /**
     * @var array<string, FilterData> filter name as key, filter data as value
     */
    private array $filters = [];

    /**
     * @param array<string, FilterData> $filters filter name as key, filter data as value
     */
    public function __construct(array $filters = [])
    {
        foreach ($filters as $name => $data) {
            $this->setFilterData($name, $data);
        }
    }

    /**
     * Creates a new instance of filtration data from an array.
     * The array keys are the filter names, and values can be either:.
     *
     * - an instance of {@see FilterData}
     * - an array of filter data
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(array_map(
            static fn (mixed $value): FilterData => (
                $value instanceof FilterData
                    ? $value
                    : FilterData::fromArray(['value' => $value])
            ),
            $data,
        ));
    }

    public static function fromDataTable(DataTableInterface $dataTable): self
    {
        $filters = [];

        foreach ($dataTable->getFilters() as $filter) {
            $filters[$filter->getName()] = new FilterData(operator: $filter->getConfig()->getDefaultOperator());
        }

        return new self($filters);
    }

    /**
     * Retrieves every defined filter data.
     *
     * @return array<string, FilterData> filter name as key, filter data as value
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * Retrieves the filter data for a given filter.
     *
     * @param string|FilterInterface $filter either the filter name or the filter instance
     */
    public function getFilterData(string|FilterInterface $filter): ?FilterData
    {
        if ($filter instanceof FilterInterface) {
            $filter = $filter->getName();
        }

        return $this->filters[$filter] ?? null;
    }

    /**
     * Updates the filter data for a given filter.
     *
     * @param string|FilterInterface $filter either the filter name or the filter instance
     */
    public function setFilterData(string|FilterInterface $filter, FilterData $data): void
    {
        if ($filter instanceof FilterInterface) {
            $filter = $filter->getName();
        }

        $this->filters[$filter] = $data;
    }

    /**
     * Completely removes the filter data for a given filter.
     *
     * @param string|FilterInterface $filter either the filter name or the filter instance
     */
    public function removeFilter(string|FilterInterface $filter): void
    {
        if ($filter instanceof FilterInterface) {
            $filter = $filter->getName();
        }

        unset($this->filters[$filter]);
    }

    /**
     * @param array<FilterInterface> $filters
     */
    public function appendMissingFilters(array $filters): void
    {
        foreach ($filters as $filter) {
            if (null !== $this->getFilterData($filter)) {
                continue;
            }

            $this->setFilterData($filter, new FilterData(operator: $filter->getConfig()->getDefaultOperator()));
        }
    }

    /**
     * @param array<FilterInterface> $filters
     */
    public function removeRedundantFilters(array $filters): void
    {
        foreach (array_diff_key($this->filters, $filters) as $name => $filter) {
            $this->removeFilter($name);
        }
    }

    public function hasActiveFilters(): bool
    {
        return $this->hasActiveFiltersExcept([]);
    }

    /**
     * @param array<string> $filterNames
     */
    public function hasActiveFiltersExcept(array $filterNames): bool
    {
        $includedFilters = array_diff_key($this->filters, array_flip($filterNames));

        foreach ($includedFilters as $filter) {
            $hasValue = $filter->hasValue();

            if ($hasValue) {
                return true;
            }
        }

        return false;
    }

    public function isEmpty(): bool
    {
        return $this->filters === [];
    }

    /**
     * @param string $offset
     */
    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->filters);
    }

    /**
     * @param string $offset
     */
    public function offsetGet(mixed $offset): FilterData
    {
        return $this->filters[$offset];
    }

    /**
     * @param string     $offset
     * @param FilterData $value
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->setFilterData($offset, $value);
    }

    /**
     * @param string $offset
     */
    public function offsetUnset(mixed $offset): void
    {
        $this->removeFilter($offset);
    }
}
