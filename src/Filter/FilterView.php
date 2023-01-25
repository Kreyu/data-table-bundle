<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

use Kreyu\Bundle\DataTableBundle\DataTableView;

class FilterView
{
    public array $vars = [
        'attr' => [],
    ];

    public function __construct(
        public ?DataTableView $parent = null,
    ) {
    }
}
