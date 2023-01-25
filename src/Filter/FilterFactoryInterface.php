<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

use Kreyu\Bundle\DataTableBundle\Filter\Type\FilterTypeInterface;

interface FilterFactoryInterface
{
    /**
     * @param class-string<FilterTypeInterface> $type
     */
    public function create(string $name, string $type, array $options = []): FilterInterface;
}
