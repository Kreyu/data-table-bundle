<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Pagination;

class PaginationFactory implements PaginationFactoryInterface
{
    public function create(PaginationContext $context): PaginationInterface
    {
        return new Pagination(
            currentPageNumber: $context->currentPageNumber,
            currentPageItemCount: $context->currentPageItemCount,
            totalItemCount: $context->totalItemCount,
            itemNumberPerPage: $context->itemNumberPerPage,
            visiblePagesRange: $context->visiblePagesRange,
        );
    }
}
