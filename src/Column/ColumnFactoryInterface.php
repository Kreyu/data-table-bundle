<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column;

use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnTypeInterface;

interface ColumnFactoryInterface
{
    /**
     * @param class-string<ColumnTypeInterface> $type
     */
    public function create(string $name, string $type, array $options = []): ColumnInterface;
}
