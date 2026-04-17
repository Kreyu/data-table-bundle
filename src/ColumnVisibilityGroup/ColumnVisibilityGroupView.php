<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\ColumnVisibilityGroup;

class ColumnVisibilityGroupView
{
    public function __construct(
        public readonly string $name,
        public readonly string $label,
        public readonly bool $isDefault,
        public readonly bool $isSelected,
    ) {
    }
}
