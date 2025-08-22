<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\ColumnVisibilityGroup;

interface ColumnVisibilityGroupBuilderInterface
{
    public function getColumnVisibilityGroup(string $name, array $options = []): ColumnVisibilityGroupInterface;
}
