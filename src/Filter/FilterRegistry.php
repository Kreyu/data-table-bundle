<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

use Kreyu\Bundle\DataTableBundle\AbstractRegistry;
use Kreyu\Bundle\DataTableBundle\Filter\Extension\FilterExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Type\FilterTypeInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Type\ResolvedFilterTypeInterface;

/**
 * @extends AbstractRegistry<FilterTypeInterface, ResolvedFilterTypeInterface, FilterExtensionInterface>
 */
class FilterRegistry extends AbstractRegistry implements FilterRegistryInterface
{
    public function getType(string $name): ResolvedFilterTypeInterface
    {
        return $this->doGetType($name);
    }

    final protected function getErrorContextName(): string
    {
        return 'filter';
    }

    final protected function getTypeClass(): string
    {
        return FilterTypeInterface::class;
    }

    final protected function getExtensionClass(): string
    {
        return FilterExtensionInterface::class;
    }
}
