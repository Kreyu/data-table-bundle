<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Fixtures\DataTable\Query;

use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Query\ResultSet;
use Kreyu\Bundle\DataTableBundle\Query\ResultSetInterface;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingData;

/**
 * Tallies getResult() calls in a static counter, shared across instances so the test observes
 * calls on the cloned query the DataTable actually runs, not the instance it was handed.
 */
class CountingProxyQuery implements ProxyQueryInterface
{
    public static int $getResultCalls = 0;

    public function __construct(
        private array $data = [],
    ) {
    }

    public static function reset(): void
    {
        self::$getResultCalls = 0;
    }

    public function sort(SortingData $sortingData): void
    {
    }

    public function paginate(PaginationData $paginationData): void
    {
    }

    public function getResult(): ResultSetInterface
    {
        ++self::$getResultCalls;

        return new ResultSet(
            iterator: new \ArrayIterator($this->data),
            currentPageItemCount: count($this->data),
            totalItemCount: count($this->data),
        );
    }
}
