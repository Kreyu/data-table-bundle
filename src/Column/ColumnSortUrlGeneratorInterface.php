<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column;

interface ColumnSortUrlGeneratorInterface
{
    public function generate(ColumnHeaderView $columnHeaderView): string;
}
