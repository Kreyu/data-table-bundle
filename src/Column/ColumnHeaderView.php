<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column;

use Kreyu\Bundle\DataTableBundle\HeaderRowView;

class ColumnHeaderView
{
    public array $vars = [
        'attr' => [],
    ];

    public function __construct(
        public HeaderRowView $parent,
    ) {
    }
}
