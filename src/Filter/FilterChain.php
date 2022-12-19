<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

class FilterChain implements FilterChainInterface
{
    private iterable $filters = [];

    /**
     * @param iterable<FilterInterface> $filters
     */
    public function __construct(iterable $filters)
    {
        foreach ($filters as $type) {
            $this->filters[get_class($type)] = $type;
        }
    }

    public function get(string $type): ?FilterInterface
    {
        return $this->filters[$type] ?? null;
    }
}
