<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Extension;

use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnTypeInterface;

interface ColumnExtensionInterface
{
    public function getType(string $name): ColumnTypeInterface;

    public function hasType(string $name): bool;

    public function getTypeExtensions(string $name): array;

    public function hasTypeExtensions(string $name): bool;
}