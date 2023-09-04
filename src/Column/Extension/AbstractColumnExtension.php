<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Extension;

use Kreyu\Bundle\DataTableBundle\AbstractExtension;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnTypeInterface;

/**
 * @extends AbstractExtension<ColumnTypeInterface, ColumnTypeExtensionInterface>
 */
abstract class AbstractColumnExtension extends AbstractExtension implements ColumnExtensionInterface
{
    public function getType(string $name): ColumnTypeInterface
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

    final protected function getTypeExtensionClass(): string
    {
        return ColumnTypeExtensionInterface::class;
    }
}