<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter\Type;

interface ResolvedFilterTypeFactoryInterface
{
    public function createResolvedType(FilterTypeInterface $type, array $typeExtensions, ResolvedFilterTypeInterface $parent = null): ResolvedFilterTypeInterface;
}