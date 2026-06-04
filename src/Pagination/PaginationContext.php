<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Pagination;

class PaginationContext
{
    public function __construct(
        public readonly int $currentPageNumber,
        public readonly int $currentPageItemCount,
        public readonly int $totalItemCount,
        public readonly ?int $itemNumberPerPage,
        public readonly int $visiblePagesRange,
    ) {
    }
}
