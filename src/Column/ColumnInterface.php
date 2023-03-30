<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column;

use Kreyu\Bundle\DataTableBundle\Column\Type\ResolvedColumnTypeInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\HeaderRowView;
use Kreyu\Bundle\DataTableBundle\ValueRowView;

interface ColumnInterface
{
    public function getName(): string;

    public function getType(): ResolvedColumnTypeInterface;

    public function getOptions(): array;

    public function createHeaderView(HeaderRowView $parent = null): ColumnHeaderView;

    public function createValueView(ValueRowView $parent = null): ColumnValueView;
}
