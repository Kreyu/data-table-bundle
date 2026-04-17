<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\ColumnVisibilityGroup;

interface ColumnVisibilityGroupFactoryInterface
{
    public function create(string $name, array $options = []): ColumnVisibilityGroupInterface;
}
