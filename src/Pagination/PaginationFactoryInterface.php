<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Pagination;

interface PaginationFactoryInterface
{
    /**
     * @throws CurrentPageOutOfRangeException
     */
    public function create(PaginationContext $context): PaginationInterface;
}
