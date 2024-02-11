<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter\Type;

use Kreyu\Bundle\DataTableBundle\Filter\Extension\FilterTypeExtensionInterface;

interface ResolvedFilterTypeFactoryInterface
{
    /**
     * @param array<FilterTypeExtensionInterface> $typeExtensions
     */
    public function createResolvedType(FilterTypeInterface $type, array $typeExtensions = [], ?ResolvedFilterTypeInterface $parent = null): ResolvedFilterTypeInterface;
}
