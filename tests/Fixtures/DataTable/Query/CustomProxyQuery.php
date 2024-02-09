<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Fixtures\DataTable\Query;

use Kreyu\Bundle\DataTableBundle\Exception\LogicException;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Query\ResultSetInterface;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingData;

class CustomProxyQuery implements ProxyQueryInterface
{
    public function sort(SortingData $sortingData): void
    {
    }

    public function paginate(PaginationData $paginationData): void
    {
    }

    public function getResult(): ResultSetInterface
    {
        throw new LogicException('Not implemented');
    }
}
