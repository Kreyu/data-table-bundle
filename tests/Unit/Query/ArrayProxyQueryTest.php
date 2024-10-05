<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Query;

use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Query\ArrayProxyQuery;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingColumnData;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingData;
use PHPUnit\Framework\TestCase;

class ArrayProxyQueryTest extends TestCase
{
    public function testGetResult()
    {
        $query = new ArrayProxyQuery(
            data: [['foo' => 'bar'], ['bar' => 'baz']],
        );

        $result = $query->getResult();

        $this->assertEquals(
            [['foo' => 'bar'], ['bar' => 'baz']],
            iterator_to_array($result->getIterator()),
        );

        $this->assertEquals(2, $result->count());
        $this->assertEquals(2, $result->getTotalItemCount());
        $this->assertEquals(2, $result->getCurrentPageItemCount());
    }

    public function testGetResultWithTotalItemCountSet()
    {
        $query = new ArrayProxyQuery(
            data: [['foo' => 'bar'], ['bar' => 'baz']],
            totalItemCount: 25,
        );

        $result = $query->getResult();

        $this->assertEquals(
            [['foo' => 'bar'], ['bar' => 'baz']],
            iterator_to_array($result->getIterator()),
        );

        $this->assertEquals(2, $result->count());
        $this->assertEquals(25, $result->getTotalItemCount());
        $this->assertEquals(2, $result->getCurrentPageItemCount());
    }

    public function testSort()
    {
        $query = new ArrayProxyQuery(
            data: [['id' => 1], ['id' => 2], ['id' => 3]],
        );

        $query->sort(new SortingData([
            new SortingColumnData('id', 'desc', '[id]'),
        ]));

        $result = $query->getResult();

        $this->assertEquals(
            [['id' => 3], ['id' => 2], ['id' => 1]],
            iterator_to_array($result->getIterator()),
        );
    }

    public function testPaginate()
    {
        $query = new ArrayProxyQuery(
            data: [['id' => 1], ['id' => 2], ['id' => 3]],
        );

        $query->paginate(new PaginationData(page: 2, perPage: 1));

        $result = $query->getResult();

        $this->assertEquals([['id' => 2]], iterator_to_array($result->getIterator()));
        $this->assertEquals(1, $result->getCurrentPageItemCount());
        $this->assertEquals(3, $result->getTotalItemCount());
    }

    public function testSortAndPaginate()
    {
        $query = new ArrayProxyQuery(
            data: [['id' => 1], ['id' => 2], ['id' => 3]],
        );

        $query->sort(new SortingData([
            new SortingColumnData('id', 'desc', '[id]'),
        ]));

        $query->paginate(new PaginationData(page: 3, perPage: 1));

        $result = $query->getResult();

        $this->assertEquals([['id' => 1]], iterator_to_array($result->getIterator()));
        $this->assertEquals(1, $result->getCurrentPageItemCount());
        $this->assertEquals(3, $result->getTotalItemCount());
    }
}
