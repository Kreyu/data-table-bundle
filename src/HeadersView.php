<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;

class HeadersView
{
    public array $columns;

    public function __construct(
        public DataTableView $parent,
    ) {
        $columns = (clone $this->parent)->vars['columns'];

        foreach ($columns as $column) {
            $this->columns[$column->getName()] = $column->createView($this->parent);
        }
    }
}
