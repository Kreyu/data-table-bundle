<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action;

use Kreyu\Bundle\DataTableBundle\DataTableView;

class ActionView
{
    public array $vars = [];

    public function __construct(
        public ?DataTableView $parent = null,
    ) {
    }
}
