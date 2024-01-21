<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column;

use Kreyu\Bundle\DataTableBundle\Column\Extension\ColumnExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnTypeInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\ResolvedColumnTypeInterface;

interface ColumnRegistryInterface
{
    /**
     * @param class-string<ColumnTypeInterface> $name
     */
    public function getType(string $name): ResolvedColumnTypeInterface;

    /**
     * @return iterable<ColumnExtensionInterface>
     */
    public function getExtensions(): iterable;
}
