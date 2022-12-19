<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column;

use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnTypeInterface;

interface ColumnInterface
{
    public function getName(): string;

    public function getType(): ColumnTypeInterface;

    public function getOptions(): array;
}
