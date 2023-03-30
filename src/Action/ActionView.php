<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action;

use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Kreyu\Bundle\DataTableBundle\DataTableView;

class ActionView
{
    public array $vars = [
        'attr' => [],
    ];

    public function __construct(
        public DataTableView|ColumnValueView $parent,
    ) {
    }
}
