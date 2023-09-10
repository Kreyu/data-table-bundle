<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter\Extension\DependencyInjection;

use Kreyu\Bundle\DataTableBundle\AbstractDependencyInjectionExtension;
use Kreyu\Bundle\DataTableBundle\Filter\Extension\FilterExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Type\FilterTypeInterface;

class DependencyInjectionFilterExtension extends AbstractDependencyInjectionExtension implements FilterExtensionInterface
{
    public function getType(string $name): FilterTypeInterface
    {
        return $this->doGetType($name);
    }

    protected function getTypeClass(): string
    {
        return FilterTypeInterface::class;
    }

    protected function getErrorContextName(): string
    {
        return 'filter';
    }
}
