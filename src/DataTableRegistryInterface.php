<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Extension\DataTableExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Type\DataTableTypeInterface;
use Kreyu\Bundle\DataTableBundle\Type\ResolvedDataTableTypeInterface;

interface DataTableRegistryInterface
{
    /**
     * @param class-string<DataTableTypeInterface> $name
     */
    public function getType(string $name): ResolvedDataTableTypeInterface;

    /**
     * @return iterable<DataTableExtensionInterface>
     */
    public function getExtensions(): iterable;
}
