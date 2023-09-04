<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Extension;

use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnTypeInterface;

class PreloadedColumnExtension extends AbstractColumnExtension
{
    /**
     * @param array<ColumnTypeInterface> $types
     * @param array<string, array<ColumnTypeExtensionInterface>> $typeExtensions
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