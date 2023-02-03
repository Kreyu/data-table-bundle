<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;

class ValuesRowView
{
    public array $vars = [];

    public function __construct(
        public DataTableView $parent,
        public mixed $data = null,
    ) {
        $columns = (clone $this->parent)->vars['columns'];

        $this->vars['columns'] = [];

        /** @var ColumnInterface $column */
        foreach ($columns as $column) {
            $column->setData($this->data);

            $this->vars['columns'][$column->getName()] = $column->createView($this->parent);
        }
    }
}
