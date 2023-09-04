<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Type\DataTableTypeInterface;

interface DataTableExtensionInterface
{
    public function getType(string $name): DataTableTypeInterface;

    public function hasType(string $name): bool;

    public function getTypeExtensions(string $name): array;

    public function hasTypeExtensions(string $name): bool;
}
