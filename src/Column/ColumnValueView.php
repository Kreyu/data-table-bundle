<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column;

use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\ValueRowView;

class ColumnValueView
{
    public array $vars = [
        'attr' => [],
    ];

    public mixed $data;
    public mixed $value;

    public function __construct(
        public ValueRowView $parent,
    ) {
    }

    public function getDataTable(): DataTableView
    {
        return $this->parent->parent;
    }
}
