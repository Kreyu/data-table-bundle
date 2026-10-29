<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Bridge\Doctrine\Orm\Query;

use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\OffsetPaginator;
use Doctrine\ORM\Tools\Pagination\Window;
use Doctrine\ORM\Tools\Pagination\WindowPage;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\DoctrineOrmResultSetFactory;
use Kreyu\Bundle\DataTableBundle\Util\RewindableGeneratorIterator;
use PHPUnit\Framework\TestCase;

class DoctrineOrmResultSetFactoryTest extends TestCase
{
    public function testCreateWithoutLimit()
    {
        $factory = new DoctrineOrmResultSetFactory();

        $resultSet = $factory->create($this->createPaginator(25), $this->createQuery(null), 10);

        $this->assertInstanceOf(RewindableGeneratorIterator::class, $resultSet->getIterator());
        $this->assertSame(range(0, end: 24), iterator_to_array($resultSet->getIterator()));
        $this->assertSame(25, $resultSet->getCurrentPageItemCount());
        $this->assertSame(25, $resultSet->getTotalItemCount());
    }

    public function testCreateWithLimit()
    {
        $factory = new DoctrineOrmResultSetFactory();

        $resultSet = $factory->create($this->createPaginator(1000), $this->createQuery(25));

        $this->assertInstanceOf(\ArrayIterator::class, $resultSet->getIterator());
        $this->assertSame(25, iterator_count($resultSet->getIterator()));
        $this->assertSame(25, $resultSet->getCurrentPageItemCount());
        $this->assertSame(1000, $resultSet->getTotalItemCount());
    }

    public function testCreateWithLimitGreaterThanBatchSize()
    {
        $factory = new DoctrineOrmResultSetFactory();

        $resultSet = $factory->create($this->createPaginator(1000), $this->createQuery(100), 25);

        $this->assertInstanceOf(\ArrayIterator::class, $resultSet->getIterator());
        $this->assertSame(100, iterator_count($resultSet->getIterator()));
        $this->assertSame(100, $resultSet->getCurrentPageItemCount());
        $this->assertSame(1000, $resultSet->getTotalItemCount());
    }

    private function createQuery(?int $maxResults): Query
    {
        $query = $this->createStub(Query::class);
        $query->method('getFirstResult')->willReturn(0);
        $query->method('getMaxResults')->willReturn($maxResults);

        return $query;
    }

    private function createPaginator(int $totalCount): OffsetPaginator
    {
        $paginator = $this->createStub(OffsetPaginator::class);
        $paginator
            ->method('paginate')
            ->willReturnCallback(static function (Query $query, Window $window) use ($totalCount): WindowPage {
                $firstResult = $window->getFirstResult();
                $lastResult = min($firstResult + $window->getMaxResults(), $totalCount) - 1;
                $items = $firstResult <= $lastResult ? range($firstResult, $lastResult) : [];

                return new WindowPage($items, $totalCount, $window);
            })
        ;

        return $paginator;
    }
}
