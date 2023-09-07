<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter\Extension;

use Kreyu\Bundle\DataTableBundle\Column\Extension\AbstractColumnExtension;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnTypeInterface;

class PreloadedFilterExtension extends AbstractColumnExtension
{
    /**
     * @param array<ColumnTypeInterface>                         $types
     * @param array<string, array<FilterTypeExtensionInterface>> $typeExtensions
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
