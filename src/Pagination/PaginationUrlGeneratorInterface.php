<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Pagination;

use Kreyu\Bundle\DataTableBundle\DataTableView;

interface PaginationUrlGeneratorInterface
{
    public function generate(DataTableView $dataTableView, int $page): string;
}
