<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Bridge\Doctrine\Orm\Paginator;

use Doctrine\ORM\Tools\Pagination\CountWalker;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Paginator\PaginatorFactory;
use Kreyu\Bundle\DataTableBundle\Tests\Unit\Bridge\Doctrine\Orm\Fixtures\Entity\Car;
use Kreyu\Bundle\DataTableBundle\Tests\Unit\Bridge\Doctrine\Orm\Fixtures\Entity\Category;
use Kreyu\Bundle\DataTableBundle\Tests\Unit\Bridge\Doctrine\Orm\Fixtures\Entity\Product;
use Kreyu\Bundle\DataTableBundle\Tests\Unit\Bridge\Doctrine\Orm\Fixtures\Entity\ProductAttribute;
use Kreyu\Bundle\DataTableBundle\Tests\Unit\Bridge\Doctrine\Orm\Fixtures\TestEntityManagerFactory;
use PHPUnit\Framework\TestCase;

class PaginatorFactoryTest extends TestCase
{
    private PaginatorFactory $paginatorFactory;

    protected function setUp(): void
    {
        $this->paginatorFactory = new PaginatorFactory();
    }

    public function testCreateWithQueryBuilderWithoutRootEntities(): void
    {
        $queryBuilder = TestEntityManagerFactory::create()->createQueryBuilder();

        $this->expectExceptionObject(new \RuntimeException('There are no root entities defined in the query.'));

        $this->paginatorFactory->create($queryBuilder);
    }

    public function testCreateWithoutJoins(): void
    {
        $queryBuilder = TestEntityManagerFactory::create()
            ->createQueryBuilder()
            ->from(Product::class, 'product');

        $paginator = $this->paginatorFactory->create($queryBuilder);

        $this->assertFalse($paginator->getQuery()->getHint(CountWalker::HINT_DISTINCT));
    }

    public function testCreateWithHints(): void
    {
        $queryBuilder = TestEntityManagerFactory::create()
            ->createQueryBuilder()
            ->from(Product::class, 'product');

        $paginator = $this->paginatorFactory->create($queryBuilder, ['foo' => 'bar']);

        $this->assertEquals('bar', $paginator->getQuery()->getHint('foo'));
    }

    public function testCreateWithSingleIdentifierAndJoins()
    {
        $queryBuilder = TestEntityManagerFactory::create()
            ->createQueryBuilder()
            ->from(Product::class, 'product')
            ->leftJoin('product.category', 'category');

        $paginator = $this->paginatorFactory->create($queryBuilder);

        $this->assertTrue($paginator->getFetchJoinCollection());
        $this->assertFalse($paginator->getUseOutputWalkers());
    }

    public function testCreateWithCompositeIdentifier()
    {
        $queryBuilder = TestEntityManagerFactory::create()
            ->createQueryBuilder()
            ->from(Car::class, 'car');

        $paginator = $this->paginatorFactory->create($queryBuilder);

        $this->assertFalse($paginator->getFetchJoinCollection());
        $this->assertNull($paginator->getUseOutputWalkers());
    }

    public function testCreateWithCompositeIdentifierAndJoins()
    {
        $queryBuilder = TestEntityManagerFactory::create()
            ->createQueryBuilder()
            ->from(Car::class, 'car')
            ->leftJoin(Product::class, 'product');

        $paginator = $this->paginatorFactory->create($queryBuilder);

        $this->assertFalse($paginator->getFetchJoinCollection());
        $this->assertNull($paginator->getUseOutputWalkers());
    }

    public function testCreateWithHaving()
    {
        $queryBuilder = TestEntityManagerFactory::create()
            ->createQueryBuilder()
            ->from(Product::class, 'product')
            ->having('product.name <> "test"');

        $paginator = $this->paginatorFactory->create($queryBuilder);

        $this->assertNull($paginator->getUseOutputWalkers());
    }

    public function testCreateWithMultipleFrom()
    {
        $queryBuilder = TestEntityManagerFactory::create()
            ->createQueryBuilder()
            ->from(Product::class, 'product')
            ->from(Category::class, 'category');

        $paginator = $this->paginatorFactory->create($queryBuilder);

        $this->assertNull($paginator->getUseOutputWalkers());
    }

    public function testCreateWithForeignKeyAsIdentifier()
    {
        $queryBuilder = TestEntityManagerFactory::create()
            ->createQueryBuilder()
            ->from(ProductAttribute::class, 'productAttribute');

        $paginator = $this->paginatorFactory->create($queryBuilder);

        $this->assertNull($paginator->getUseOutputWalkers());
    }

    public function testCreateWithJoinsAndOrderBy()
    {
        $queryBuilder = TestEntityManagerFactory::create()
            ->createQueryBuilder()
            ->from(Product::class, 'product')
            ->leftJoin('product.category', 'category')
            ->orderBy('product.id', 'ASC');

        $paginator = $this->paginatorFactory->create($queryBuilder);

        $this->assertFalse($paginator->getUseOutputWalkers());
    }

    public function testCreateWithOrderByAssociation()
    {
        $queryBuilder = TestEntityManagerFactory::create()
            ->createQueryBuilder()
            ->from(Product::class, 'product')
            ->leftJoin('product.category', 'category')
            ->orderBy('category.id', 'ASC');

        $paginator = $this->paginatorFactory->create($queryBuilder);

        $this->assertNull($paginator->getUseOutputWalkers());
    }
}
