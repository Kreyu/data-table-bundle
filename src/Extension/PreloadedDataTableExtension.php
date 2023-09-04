<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Extension;

use Kreyu\Bundle\DataTableBundle\Exception\InvalidArgumentException;
use Kreyu\Bundle\DataTableBundle\Type\DataTableTypeInterface;

class PreloadedDataTableExtension extends AbstractDataTableExtension
{
    /**
     * @param array<DataTableTypeInterface> $types
     * @param array<string, array<DataTableTypeExtensionInterface>> $typeExtensions
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
