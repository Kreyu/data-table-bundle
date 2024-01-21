<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter\Extension;

use Kreyu\Bundle\DataTableBundle\Filter\Type\FilterTypeInterface;

class PreloadedFilterExtension extends AbstractFilterExtension
{
    /**
     * @param array<FilterTypeInterface> $types
     * @param array<FilterTypeExtensionInterface>|array<string, array<FilterTypeExtensionInterface>> $typeExtensions
     */
    public function __construct(
        private readonly array $types = [],
        private readonly array $typeExtensions = [],
    ) {
    }

    protected function loadTypes(): array
    {
        return $this->types;
    }

    protected function loadTypeExtensions(): array
    {
        return $this->typeExtensions;
    }
}
