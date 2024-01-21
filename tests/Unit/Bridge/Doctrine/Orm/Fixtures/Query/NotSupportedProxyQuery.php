<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Bridge\Doctrine\Orm\Fixtures\Query;

use Kreyu\Bundle\DataTableBundle\Exception\BadMethodCallException;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationInterface;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Query\ResultSetInterface;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingData;

class NotSupportedProxyQuery implements ProxyQueryInterface
{
    public function getItems(): iterable
    {
        throw new BadMethodCallException('Not supported');
    }

    public function getResult(): ResultSetInterface
    {
        throw new BadMethodCallException('Not supported');
    }

    public function getPagination(): PaginationInterface
    {
        throw new BadMethodCallException('Not supported');
    }

    public function sort(SortingData $sortingData): void
    {
        throw new BadMethodCallException('Not supported');
    }

    public function paginate(PaginationData $paginationData): void
    {
        throw new BadMethodCallException('Not supported');
    }
}
