<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Extension\DependencyInjection;

use Kreyu\Bundle\DataTableBundle\AbstractDependencyInjectionExtension;
use Kreyu\Bundle\DataTableBundle\Column\Extension\ColumnExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnTypeInterface;

class DependencyInjectionColumnExtension extends AbstractDependencyInjectionExtension implements ColumnExtensionInterface
{
    public function getType(string $name): ColumnTypeInterface
    {
        return $this->doGetType($name);
    }

    protected function getTypeClass(): string
    {
        return ColumnTypeInterface::class;
    }

    protected function getErrorContextName(): string
    {
        return 'column';
    }
}
