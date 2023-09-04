<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column;

use Kreyu\Bundle\DataTableBundle\AbstractRegistry;
use Kreyu\Bundle\DataTableBundle\Column\Extension\ColumnExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnTypeInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\ResolvedColumnTypeInterface;

/**
 * @extends AbstractRegistry<ColumnTypeInterface, ResolvedColumnTypeInterface, ColumnExtensionInterface>
 */
class ColumnRegistry extends AbstractRegistry implements ColumnRegistryInterface
{
    public function getType(string $name): ResolvedColumnTypeInterface
    {
        return $this->doGetType($name);
    }

    final protected function getErrorContextName(): string
    {
        return 'column';
    }

    final protected function getTypeClass(): string
    {
        return ColumnTypeInterface::class;
    }

    final protected function getExtensionClass(): string
    {
        return ColumnExtensionInterface::class;
    }
}
