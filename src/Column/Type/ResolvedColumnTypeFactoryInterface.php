<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\Extension\ColumnTypeExtensionInterface;

interface ResolvedColumnTypeFactoryInterface
{
    /**
     * @param array<ColumnTypeExtensionInterface> $typeExtensions
     */
    public function createResolvedType(ColumnTypeInterface $type, array $typeExtensions = [], ResolvedColumnTypeInterface $parent = null): ResolvedColumnTypeInterface;
}
