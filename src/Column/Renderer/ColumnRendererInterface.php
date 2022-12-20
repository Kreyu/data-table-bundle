<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Renderer;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\View\DataTableViewInterface;

interface ColumnRendererInterface
{
    public function renderHeader(DataTableViewInterface $dataTable, ColumnInterface $column): string;

    public function renderValue(DataTableViewInterface $dataTable, ColumnInterface $column, mixed $value): string;
}
