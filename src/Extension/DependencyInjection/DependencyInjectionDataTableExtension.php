<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Extension\DependencyInjection;

use Kreyu\Bundle\DataTableBundle\AbstractDependencyInjectionExtension;
use Kreyu\Bundle\DataTableBundle\Extension\DataTableExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Type\DataTableTypeInterface;

class DependencyInjectionDataTableExtension extends AbstractDependencyInjectionExtension implements DataTableExtensionInterface
{
    public function getType(string $name): DataTableTypeInterface
    {
        return $this->doGetType($name);
    }

    protected function getTypeClass(): string
    {
        return DataTableTypeInterface::class;
    }

    protected function getErrorContextName(): string
    {
        return 'data table';
    }
}
