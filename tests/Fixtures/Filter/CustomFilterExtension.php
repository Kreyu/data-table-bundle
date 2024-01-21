<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Filter;

use Kreyu\Bundle\DataTableBundle\Exception\LogicException;
use Kreyu\Bundle\DataTableBundle\Filter\Extension\FilterExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Type\FilterTypeInterface;

class CustomFilterExtension implements FilterExtensionInterface
{
    public function getType(string $name): FilterTypeInterface
    {
        throw new LogicException('Not implemented');
    }

    public function hasType(string $name): bool
    {
        throw new LogicException('Not implemented');
    }

    public function getTypeExtensions(string $name): array
    {
        throw new LogicException('Not implemented');
    }

    public function hasTypeExtensions(string $name): bool
    {
        throw new LogicException('Not implemented');
    }
}
