<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

readonly class FiltrationData
{
    public function __construct(
        private array $filters = [],
    ) {
    }

    public static function fromArray(array $data): static
    {
        return new static(array_map(
            fn (array $data) => FilterData::fromArray($data),
            $data,
        ));
    }

    public function toArray(): array
    {
        return array_map(
            fn (FilterData $filter) => $filter->toArray(),
            $this->filters
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
