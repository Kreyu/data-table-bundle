<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

use Kreyu\Bundle\DataTableBundle\Exception\UnexpectedTypeException;
use Traversable;

/**
 * @extends \IteratorAggregate<string, FilterData>
 */
readonly class FiltrationData implements \IteratorAggregate
{
    private array $filters;

    /**
     * @param array<string, FilterData> $filters filter name as key, filter data as value
     */
    public function __construct(array $filters = [])
    {
        foreach ($filters as $filter) {
            if (!$filter instanceof FilterData) {
                throw new UnexpectedTypeException($filter, FilterData::class);
            }
        }

        $this->filters = $filters;
    }

    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->filters);
    }

    /**
     * Creates a new instance of filtration data from an array.
     * The array keys are the filter names, and values can be either:.
     *
     * - an instance of {@see FilterData}
     * - an array of filter data
     */
    public static function fromArray(array $data): self
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

        return new self($filters);
    }

    /**
     * @return array<string, FilterData> filter name as key, filter data as value
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @param string|FilterInterface $filter either the filter name or the filter instance
     */
    public function getFilterData(string|FilterInterface $filter): ?FilterData
    {
        if ($filter instanceof FilterInterface) {
            $filter = $filter->getName();
        }

        return $this->filters[$filter] ?? null;
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

    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->filters);
    }

    public function offsetGet(mixed $offset): FilterData
    {
        return $this->filters[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
    }

    public function offsetUnset(mixed $offset): void
    {
    }
}
