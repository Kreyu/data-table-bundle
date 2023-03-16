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
            if ($value instanceof FilterData) {
                $filters[$key] = $value;
            } elseif (is_array($value)) {
                if (!array_key_exists('value', $value)) {
                    $value = ['value' => ''];
                }

                $filters[$key] = FilterData::fromArray($value);
            } else {
                $filters[$key] = FilterData::fromArray([
                    'value' => $value,
                ]);
            }
        }

        return new static($filters);
    }

    public function toArray(): array
    {
        return array_map(
            fn (FilterData $filter) => $filter->toArray(),
            $this->filters,
        );
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function getFilter(string $name): ?FilterData
    {
        return $this->filters[$name] ?? null;
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
