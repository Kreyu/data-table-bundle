<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column;

use Kreyu\Bundle\DataTableBundle\DataTableView;

class ColumnView
{
    public array $vars = [
        'attr' => [],
    ];

    public array $children = [];

    public function __construct(
        public ?DataTableView $parent = null,
    ) {
    }
}
