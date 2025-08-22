<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\ColumnVisibilityGroup;

interface ColumnVisibilityGroupInterface
{
    public function getName(): string;
    public function getLabel(): string;
    public function isDefault(): bool;
    public function setIsDefault(bool $isDefault): self;
}
