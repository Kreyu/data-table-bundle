<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Type;

class ResolvedDataTableTypeFactory implements ResolvedDataTableTypeFactoryInterface
{
    public function createResolvedType(DataTableTypeInterface $type, array $typeExtensions, ResolvedDataTableTypeInterface $parent = null): ResolvedDataTableTypeInterface
    {
        return new ResolvedDataTableType($type, $typeExtensions, $parent);
    }
}
