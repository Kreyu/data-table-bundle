<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Query;

use Kreyu\Bundle\DataTableBundle\Query\ResultSet;
use PHPUnit\Framework\TestCase;

class ResultSetTest extends TestCase
{
    public function testGetTotalItemCount(): void
    {
        $resultSet = new ResultSet(new \ArrayIterator(), 10, 20);

        $this->assertSame(20, $resultSet->getTotalItemCount());
    }

    public function testGetTotalItemCountWhenNotSet(): void
    {
        $resultSet = new ResultSet(new \ArrayIterator(), 10);

        $this->assertSame(10, $resultSet->getTotalItemCount());
    }

    public function testCount(): void
    {
        $resultSet = new ResultSet(new \ArrayIterator(), 10, 20);

        $this->assertSame(10, $resultSet->count());
    }

    public function testCountWhenCurrentPageItemCountNotSet(): void
    {
        $resultSet = new ResultSet(new \ArrayIterator(), null, 20);

        $this->assertSame(20, $resultSet->count());
    }
}
