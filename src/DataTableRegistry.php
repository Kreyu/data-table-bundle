<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Extension\DataTableExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Type\DataTableTypeInterface;
use Kreyu\Bundle\DataTableBundle\Type\ResolvedDataTableTypeInterface;

/**
 * @extends AbstractRegistry<DataTableTypeInterface, ResolvedDataTableTypeInterface, DataTableExtensionInterface>
 */
class DataTableRegistry extends AbstractRegistry implements DataTableRegistryInterface
{
    public function getType(string $name): ResolvedDataTableTypeInterface
    {
        return $this->doGetType($name);
    }

    final protected function getErrorContextName(): string
    {
        return 'data table';
    }

    final protected function getTypeClass(): string
    {
        return DataTableTypeInterface::class;
    }

    final protected function getExtensionClass(): string
    {
        return DataTableExtensionInterface::class;
    }
}
