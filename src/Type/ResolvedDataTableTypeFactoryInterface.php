<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Type;

interface ResolvedDataTableTypeFactoryInterface
{
    public function createResolvedType(DataTableTypeInterface $type, array $typeExtensions, ResolvedDataTableTypeInterface $parent = null): ResolvedDataTableTypeInterface;
}
