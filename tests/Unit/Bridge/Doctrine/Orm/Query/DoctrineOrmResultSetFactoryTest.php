<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Bridge\Doctrine\Orm\Query;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\DoctrineOrmResultSetFactory;
use Kreyu\Bundle\DataTableBundle\Util\RewindableGeneratorIterator;
use PHPUnit\Framework\TestCase;

class DoctrineOrmResultSetFactoryTest extends TestCase
{
    public function testCreateWithoutLimit()
    {
        $paginator = $this->createMock(Paginator::class);
        $paginator->method('count')->willReturn(25);
        $paginator->method('getIterator')->willReturn(new \ArrayIterator(array_fill(0, 25, 'item')));

        $query = $this->createMock(Query::class);
        $query->method('getFirstResult')->willReturn(0);
        $query->method('getMaxResults')->willReturn(null);
        $query->method('getEntityManager')->willReturn($this->createMock(EntityManagerInterface::class));

        $paginator->method('getQuery')->willReturn($query);

        $factory = new DoctrineOrmResultSetFactory();

        $resultSet = $factory->create($paginator);

        $this->assertInstanceOf(RewindableGeneratorIterator::class, $resultSet->getIterator());
        $this->assertEquals(25, iterator_count($resultSet->getIterator()));
        $this->assertEquals(25, $resultSet->getCurrentPageItemCount());
        $this->assertEquals(25, $resultSet->getTotalItemCount());
    }

    public function testCreateWithLimit()
    {
        $paginator = $this->createMock(Paginator::class);
        $paginator->method('count')->willReturn(1000);
        $paginator->method('getIterator')->willReturn(new \ArrayIterator(array_fill(0, 25, 'item')));

        $query = $this->createMock(Query::class);
        $query->method('getFirstResult')->willReturn(0);
        $query->method('getMaxResults')->willReturn(25);
        $query->method('getEntityManager')->willReturn($this->createMock(EntityManagerInterface::class));

        $paginator->method('getQuery')->willReturn($query);

        $factory = new DoctrineOrmResultSetFactory();

        $resultSet = $factory->create($paginator);

        $this->assertInstanceOf(\ArrayIterator::class, $resultSet->getIterator());
        $this->assertEquals(25, iterator_count($resultSet->getIterator()));
        $this->assertEquals(25, $resultSet->getCurrentPageItemCount());
        $this->assertEquals(1000, $resultSet->getTotalItemCount());
    }

    public function testCreateWithLimitGreaterThanBatchSize()
    {
        $paginator = $this->createMock(Paginator::class);
        $paginator->method('count')->willReturn(1000);
        $paginator->method('getIterator')->willReturn(new \ArrayIterator(array_fill(0, 25, 'item')));

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->exactly(4))->method('clear');

        $query = $this->createMock(Query::class);
        $query->method('getFirstResult')->willReturn(0);
        $query->method('getMaxResults')->willReturn(100);
        $query->method('getEntityManager')->willReturn($entityManager);

        $paginator->method('getQuery')->willReturn($query);

        $factory = new DoctrineOrmResultSetFactory();

        $resultSet = $factory->create($paginator, 25);

        $this->assertInstanceOf(\ArrayIterator::class, $resultSet->getIterator());
        $this->assertEquals(100, iterator_count($resultSet->getIterator()));
        $this->assertEquals(100, $resultSet->getCurrentPageItemCount());
        $this->assertEquals(1000, $resultSet->getTotalItemCount());
    }
}
