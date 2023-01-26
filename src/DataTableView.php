<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

class DataTableView
{
    public array $vars = [
        'attr' => [],
    ];

    public function createRowView(mixed $data = null): RowView
    {
        return new RowView($this, $data);
    }
}
