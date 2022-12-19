<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

interface FilterChainInterface
{
    /**
     * @param class-string<FilterInterface> $type
     */
    public function get(string $type): ?FilterInterface;
}
