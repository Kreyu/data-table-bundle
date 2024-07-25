<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

use Kreyu\Bundle\DataTableBundle\DataTableView;

interface FilterClearUrlGeneratorInterface
{
    public function generate(DataTableView $dataTableView, FilterView ...$filterViews): string;
}
