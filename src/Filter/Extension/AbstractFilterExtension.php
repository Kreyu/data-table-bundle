<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter\Extension;

use Kreyu\Bundle\DataTableBundle\AbstractExtension;
use Kreyu\Bundle\DataTableBundle\Filter\Type\FilterTypeInterface;

/**
 * @extends AbstractExtension<FilterTypeInterface, FilterTypeExtensionInterface>
 */
abstract class AbstractFilterExtension extends AbstractExtension implements FilterExtensionInterface
{
    public function getType(string $name): FilterTypeInterface
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

    final protected function getTypeExtensionClass(): string
    {
        return FilterTypeExtensionInterface::class;
    }
}
