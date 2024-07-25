<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Pagination;

interface PaginationUrlGeneratorInterface
{
    public function generate(PaginationView $paginationView, int $page): string;
}
