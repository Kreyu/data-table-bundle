<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Extension;

use Kreyu\Bundle\DataTableBundle\AbstractExtension;
use Kreyu\Bundle\DataTableBundle\Type\DataTableTypeInterface;

/**
 * @extends AbstractExtension<DataTableTypeInterface, DataTableTypeExtensionInterface>
 */
abstract class AbstractDataTableExtension extends AbstractExtension implements DataTableExtensionInterface
{
    public function getType(string $name): DataTableTypeInterface
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

    final protected function getTypeExtensionClass(): string
    {
        return DataTableTypeExtensionInterface::class;
    }
}
