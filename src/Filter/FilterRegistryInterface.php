<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

use Kreyu\Bundle\DataTableBundle\Filter\Extension\FilterExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Type\FilterTypeInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Type\ResolvedFilterTypeInterface;

interface FilterRegistryInterface
{
    /**
     * @param class-string<FilterTypeInterface> $name
     */
    public function getType(string $name): ResolvedFilterTypeInterface;

    /**
     * @return iterable<FilterExtensionInterface>
     */
    public function getExtensions(): iterable;
}
