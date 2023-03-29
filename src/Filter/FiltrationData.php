<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

use Kreyu\Bundle\DataTableBundle\Exception\UnexpectedTypeException;

class FiltrationData
{
    /**
     * @param array<FilterData> $filters
     */
    public function __construct(
        private array $filters = [],
    ) {
        foreach ($filters as $filter) {
            if (!$filter instanceof FilterData) {
                throw new UnexpectedTypeException($filter, FilterData::class);
            }
        }
    }

    public static function fromArray(array $data): static
    {
        $filters = [];

        foreach ($data as $key => $value) {
            if (!$value instanceof FilterData) {
                $value = ['value' => $value];
            }

            if (is_array($value)) {
                $value = FilterData::fromArray($value);
            }

            $filters[$key] = $value;
        }

        return new static($filters);
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function getFilterData(string|FilterInterface $filter): ?FilterData
    {
        if ($filter instanceof FilterInterface) {
            $filter = $filter->getName();
        }

        return $this->filters[$filter] ?? null;
    }

    public function setFilterData(string|FilterInterface $filter, FilterData $data): void
    {
        if ($filter instanceof FilterInterface) {
            $filter = $filter->getName();
        }

        $this->filters[$filter] = $data;
    }

    /**
     * @param array<FilterInterface> $filters
     */
    public function appendMissingFilters(array $filters, FilterData $data = new FilterData): void
    {
        foreach ($filters as $column) {
            if (null === $this->getFilterData($column)) {
                $this->setFilterData($column, $data);
            }
        }
    }

    public function hasActiveFilters(): bool
    {
        foreach ($this->filters as $filter) {
            if ($filter->hasValue()) {
                return true;
            }
        }

        return false;
    }

    public function isEmpty(): bool
    {
        return empty($this->filters);
    }
}
