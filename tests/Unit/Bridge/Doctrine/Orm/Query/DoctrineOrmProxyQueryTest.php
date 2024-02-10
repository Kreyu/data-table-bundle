<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Bridge\Doctrine\Orm\Query;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Paginator\PaginatorFactory;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Paginator\PaginatorFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\AliasResolver;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\AliasResolverInterface;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\DoctrineOrmProxyQuery;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\DoctrineOrmResultSetFactory;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\DoctrineOrmResultSetFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Exception\InvalidArgumentException;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingColumnData;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingData;
use Kreyu\Bundle\DataTableBundle\Tests\Unit\Bridge\Doctrine\Orm\Fixtures\Entity\Product;
use Kreyu\Bundle\DataTableBundle\Tests\Unit\Bridge\Doctrine\Orm\Fixtures\TestEntityManagerFactory;
use PHPUnit\Framework\TestCase;

class DoctrineOrmProxyQueryTest extends TestCase
{
    public function testMagicCallMethod()
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->once())->method('getDQL');

        $proxyQuery = new DoctrineOrmProxyQuery($queryBuilder);
        $proxyQuery->getDQL();
    }

    public function testCloning()
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);

        $proxyQuery = new DoctrineOrmProxyQuery($queryBuilder);

        $this->assertNotSame($proxyQuery->getQuery(), (clone $proxyQuery)->getQueryBuilder());
    }

    public function testSort()
    {
        $queryBuilder = TestEntityManagerFactory::create()
            ->createQueryBuilder()
            ->from(Product::class, 'product')
            ->leftJoin('product.category', 'category')
            ->orderBy('product.id', 'ASC');

        $proxyQuery = new DoctrineOrmProxyQuery($queryBuilder);
        $proxyQuery->sort(new SortingData([
            new SortingColumnData('product.name', 'ASC'),
            new SortingColumnData('category.name', 'DESC'),
        ]));

        $orderBy = $queryBuilder->getDQLPart('orderBy');

        $this->assertCount(2, $orderBy);
        $this->assertEquals(['product.name ASC'], $orderBy[0]->getParts());
        $this->assertEquals(['category.name DESC'], $orderBy[1]->getParts());
    }

    public function testPaginate()
    {
        $queryBuilder = TestEntityManagerFactory::create()
            ->createQueryBuilder()
            ->from(Product::class, 'product');

        $proxyQuery = new DoctrineOrmProxyQuery($queryBuilder);
        $proxyQuery->paginate(new PaginationData(1, 25));

        $this->assertEquals(0, $queryBuilder->getFirstResult());
        $this->assertEquals(25, $queryBuilder->getMaxResults());
    }

    public function testGetResult()
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);

        $paginator = $this->createMock(Paginator::class);
        $paginator->method('getQuery')->willReturn($this->createMock(Query::class));

        $paginationFactory = $this->createMock(PaginatorFactoryInterface::class);
        $paginationFactory->expects($this->once())
            ->method('create')
            ->with($queryBuilder, ['foo' => 'bar'])
            ->willReturn($paginator);

        $resultSetFactory = $this->createMock(DoctrineOrmResultSetFactoryInterface::class);
        $resultSetFactory->expects($this->once())
            ->method('create')
            ->with($paginator, 5000);

        $proxyQuery = new DoctrineOrmProxyQuery($queryBuilder, ['foo' => 'bar']);
        $proxyQuery->setResultSetFactory($resultSetFactory);
        $proxyQuery->setPaginatorFactory($paginationFactory);
        $proxyQuery->getResult();
    }

    public function testGetQueryBuilder()
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);

        $proxyQuery = new DoctrineOrmProxyQuery($queryBuilder);

        $this->assertSame($queryBuilder, $proxyQuery->getQueryBuilder());
    }

    public function testGetUniqueParameterId()
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);

        $proxyQuery = new DoctrineOrmProxyQuery($queryBuilder);

        $this->assertEquals(0, $proxyQuery->getUniqueParameterId());
        $this->assertEquals(1, $proxyQuery->getUniqueParameterId());
        $this->assertEquals(2, $proxyQuery->getUniqueParameterId());
    }

    public function testGetHints()
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);

        $proxyQuery = new DoctrineOrmProxyQuery($queryBuilder, ['foo' => 'bar']);

        $this->assertEquals(['foo' => 'bar'], $proxyQuery->getHints());
    }

    public function testSetHint()
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);

        $proxyQuery = new DoctrineOrmProxyQuery($queryBuilder);
        $proxyQuery->setHint('foo', 'bar');

        $this->assertEquals(['foo' => 'bar'], $proxyQuery->getHints());
    }

    public function testGetDefaultHydrationMode()
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);

        $query = $this->createMock(Query::class);
        $query->expects($this->once())->method('setHydrationMode')->with(AbstractQuery::HYDRATE_OBJECT);

        $paginator = $this->createMock(Paginator::class);
        $paginator->method('getQuery')->willReturn($query);

        $paginatorFactory = $this->createMock(PaginatorFactoryInterface::class);
        $paginatorFactory->method('create')->willReturn($paginator);

        $proxyQuery = new DoctrineOrmProxyQuery($queryBuilder);
        $proxyQuery->setPaginatorFactory($paginatorFactory);

        $this->assertEquals(AbstractQuery::HYDRATE_OBJECT, $proxyQuery->getHydrationMode());

        $proxyQuery->getResult();
    }

    public function testSetHydrationMode()
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);

        $query = $this->createMock(Query::class);
        $query->expects($this->once())->method('setHydrationMode')->with(AbstractQuery::HYDRATE_ARRAY);

        $paginator = $this->createMock(Paginator::class);
        $paginator->method('getQuery')->willReturn($query);

        $paginatorFactory = $this->createMock(PaginatorFactoryInterface::class);
        $paginatorFactory->method('create')->willReturn($paginator);

        $proxyQuery = new DoctrineOrmProxyQuery($queryBuilder);
        $proxyQuery->setPaginatorFactory($paginatorFactory);
        $proxyQuery->setHydrationMode(AbstractQuery::HYDRATE_ARRAY);

        $this->assertEquals(AbstractQuery::HYDRATE_ARRAY, $proxyQuery->getHydrationMode());

        $proxyQuery->getResult();
    }

    public function testGetDefaultBatchSize()
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);

        $proxyQuery = new DoctrineOrmProxyQuery($queryBuilder);

        $this->assertEquals(5000, $proxyQuery->getBatchSize());
    }

    public function testSetBatchSizeZero()
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);

        $proxyQuery = new DoctrineOrmProxyQuery($queryBuilder);

        $this->expectExceptionObject(new InvalidArgumentException('The batch size must be positive.'));

        $proxyQuery->setBatchSize(0);
    }

    public function testSetBatchSizeNegative()
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);

        $proxyQuery = new DoctrineOrmProxyQuery($queryBuilder);

        $this->expectExceptionObject(new InvalidArgumentException('The batch size must be positive.'));

        $proxyQuery->setBatchSize(-1);
    }

    public function testSetBatchSize()
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);

        $proxyQuery = new DoctrineOrmProxyQuery($queryBuilder);
        $proxyQuery->setBatchSize(25);

        $this->assertEquals(25, $proxyQuery->getBatchSize());
    }

    public function testGetDefaultPaginatorFactory()
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);

        $proxyQuery = new DoctrineOrmProxyQuery($queryBuilder);

        $this->assertInstanceOf(PaginatorFactory::class, $proxyQuery->getPaginatorFactory());
    }

    public function testSetPaginatorFactory()
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $aliasResolver = $this->createMock(PaginatorFactoryInterface::class);

        $proxyQuery = new DoctrineOrmProxyQuery($queryBuilder);
        $proxyQuery->setPaginatorFactory($aliasResolver);

        $this->assertEquals($aliasResolver, $proxyQuery->getPaginatorFactory());
    }

    public function testGetDefaultAliasResolver()
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);

        $proxyQuery = new DoctrineOrmProxyQuery($queryBuilder);

        $this->assertInstanceOf(AliasResolver::class, $proxyQuery->getAliasResolver());
    }

    public function testSetAliasResolver()
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $aliasResolver = $this->createMock(AliasResolverInterface::class);

        $proxyQuery = new DoctrineOrmProxyQuery($queryBuilder);
        $proxyQuery->setAliasResolver($aliasResolver);

        $this->assertEquals($aliasResolver, $proxyQuery->getAliasResolver());
    }

    public function testGetDefaultResultSetFactory()
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);

        $proxyQuery = new DoctrineOrmProxyQuery($queryBuilder);

        $this->assertInstanceOf(DoctrineOrmResultSetFactory::class, $proxyQuery->getResultSetFactory());
    }

    public function testSetResultSetFactory()
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $resultSetFactory = $this->createMock(DoctrineOrmResultSetFactoryInterface::class);

        $proxyQuery = new DoctrineOrmProxyQuery($queryBuilder);
        $proxyQuery->setResultSetFactory($resultSetFactory);

        $this->assertEquals($resultSetFactory, $proxyQuery->getResultSetFactory());
    }
}
