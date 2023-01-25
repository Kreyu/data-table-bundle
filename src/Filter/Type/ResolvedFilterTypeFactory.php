<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter\Type;

class ResolvedFilterTypeFactory implements ResolvedFilterTypeFactoryInterface
{
    public function createResolvedType(FilterTypeInterface $type, array $typeExtensions, ResolvedFilterTypeInterface $parent = null): ResolvedFilterTypeInterface
    {
        return new ResolvedFilterType($type, $typeExtensions, $parent);
    }
}
