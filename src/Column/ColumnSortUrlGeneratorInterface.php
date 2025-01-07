<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column;

use Kreyu\Bundle\DataTableBundle\DataTableView;

interface ColumnSortUrlGeneratorInterface
{
    public function generate(DataTableView $dataTableView, ColumnHeaderView ...$columnHeaderView): string;
}
