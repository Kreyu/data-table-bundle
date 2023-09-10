<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Type;

use Kreyu\Bundle\DataTableBundle\Extension\DataTableTypeExtensionInterface;

interface ResolvedDataTableTypeFactoryInterface
{
    /**
     * @param array<DataTableTypeExtensionInterface> $typeExtensions
     */
    public function createResolvedType(DataTableTypeInterface $type, array $typeExtensions, ResolvedDataTableTypeInterface $parent = null): ResolvedDataTableTypeInterface;
}
