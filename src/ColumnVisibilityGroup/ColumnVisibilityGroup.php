<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\ColumnVisibilityGroup;

class ColumnVisibilityGroup implements ColumnVisibilityGroupInterface
{
    public function __construct(
        private readonly string $name,
        private readonly string $label,
        private readonly bool $isDefault = false,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
    }
}
