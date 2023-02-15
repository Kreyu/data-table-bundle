<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

class HeadersRowView
{
    public array $vars = [];

    public function __construct(
        public DataTableView $parent,
    ) {
        $columns = (clone $parent)->vars['columns'];

        $this->vars['columns'] = [];

        foreach ($columns as $column) {
            $this->vars['columns'][$column->getName()] = $column->createView($parent);
        }
    }
}
