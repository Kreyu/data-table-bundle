<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter\Extension;

use Kreyu\Bundle\DataTableBundle\Filter\Type\FilterTypeInterface;

interface FilterExtensionInterface
{
    public function getType(string $name): FilterTypeInterface;

    public function hasType(string $name): bool;

    public function getTypeExtensions(string $name): array;

    public function hasTypeExtensions(string $name): bool;
}