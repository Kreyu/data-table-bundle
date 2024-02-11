<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

class ResolvedColumnTypeFactory implements ResolvedColumnTypeFactoryInterface
{
    public function createResolvedType(ColumnTypeInterface $type, array $typeExtensions = [], ?ResolvedColumnTypeInterface $parent = null): ResolvedColumnTypeInterface
    {
        return new ResolvedColumnType($type, $typeExtensions, $parent);
    }
}
