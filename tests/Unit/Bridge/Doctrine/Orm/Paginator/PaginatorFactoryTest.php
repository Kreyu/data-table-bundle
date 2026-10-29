<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Bridge\Doctrine\Orm\Paginator;

use Doctrine\ORM\Tools\Pagination\CountWalker;
use Doctrine\ORM\Tools\Pagination\OffsetPaginator;
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

        $this->paginatorFactory->create($queryBuilder, $queryBuilder->getQuery());
    }

    public function testCreateWithoutJoins(): void
    {
        $queryBuilder = TestEntityManagerFactory::create()
            ->createQueryBuilder()
            ->from(Product::class, 'product')
        ;

        $query = $queryBuilder->getQuery();

        $this->paginatorFactory->create($queryBuilder, $query);

        $this->assertFalse($query->getHint(CountWalker::HINT_DISTINCT));
    }

    public function testCreateWithHints(): void
    {
        $queryBuilder = TestEntityManagerFactory::create()
            ->createQueryBuilder()
            ->from(Product::class, 'product')
        ;

        $query = $queryBuilder->getQuery();

        $this->paginatorFactory->create($queryBuilder, $query, ['foo' => 'bar']);

        $this->assertSame('bar', $query->getHint('foo'));
    }

    public function testCreateWithSingleIdentifierAndJoins()
    {
        $queryBuilder = TestEntityManagerFactory::create()
            ->createQueryBuilder()
            ->from(Product::class, 'product')
            ->leftJoin('product.category', 'category')
        ;

        $paginator = $this->paginatorFactory->create($queryBuilder, $queryBuilder->getQuery());

        $this->assertTrue($paginator->getFetchJoinCollection());
        $this->assertFalse($this->getUseOutputWalkers($paginator));
        $this->assertTrue($this->paginatorFactory->canDisableOutputWalkers($queryBuilder));
    }

    public function testCreateWithCompositeIdentifier()
    {
        $queryBuilder = TestEntityManagerFactory::create()
            ->createQueryBuilder()
            ->from(Car::class, 'car')
        ;

        $paginator = $this->paginatorFactory->create($queryBuilder, $queryBuilder->getQuery());

        $this->assertFalse($paginator->getFetchJoinCollection());
        $this->assertNull($this->getUseOutputWalkers($paginator));
        $this->assertFalse($this->paginatorFactory->canDisableOutputWalkers($queryBuilder));
    }

    public function testCreateWithCompositeIdentifierAndJoins()
    {
        $queryBuilder = TestEntityManagerFactory::create()
            ->createQueryBuilder()
            ->from(Car::class, 'car')
            ->leftJoin(Product::class, 'product')
        ;

        $paginator = $this->paginatorFactory->create($queryBuilder, $queryBuilder->getQuery());

        $this->assertFalse($paginator->getFetchJoinCollection());
        $this->assertNull($this->getUseOutputWalkers($paginator));
        $this->assertFalse($this->paginatorFactory->canDisableOutputWalkers($queryBuilder));
    }

    public function testCreateWithHaving()
    {
        $queryBuilder = TestEntityManagerFactory::create()
            ->createQueryBuilder()
            ->from(Product::class, 'product')
            ->having('product.name <> "test"')
        ;

        $this->assertFalse($this->paginatorFactory->canDisableOutputWalkers($queryBuilder));
    }

    public function testCreateWithMultipleFrom()
    {
        $queryBuilder = TestEntityManagerFactory::create()
            ->createQueryBuilder()
            ->from(Product::class, 'product')
            ->from(Category::class, 'category')
        ;

        $this->assertFalse($this->paginatorFactory->canDisableOutputWalkers($queryBuilder));
    }

    public function testCreateWithForeignKeyAsIdentifier()
    {
        $queryBuilder = TestEntityManagerFactory::create()
            ->createQueryBuilder()
            ->from(ProductAttribute::class, 'productAttribute')
        ;

        $this->assertFalse($this->paginatorFactory->canDisableOutputWalkers($queryBuilder));
    }

    public function testCreateWithJoinsAndOrderBy()
    {
        $queryBuilder = TestEntityManagerFactory::create()
            ->createQueryBuilder()
            ->from(Product::class, 'product')
            ->leftJoin('product.category', 'category')
            ->orderBy('product.id', 'ASC')
        ;

        $this->assertTrue($this->paginatorFactory->canDisableOutputWalkers($queryBuilder));
    }

    public function testCreateWithOrderByAssociation()
    {
        $queryBuilder = TestEntityManagerFactory::create()
            ->createQueryBuilder()
            ->from(Product::class, 'product')
            ->leftJoin('product.category', 'category')
            ->orderBy('category.id', 'ASC')
        ;

        $this->assertFalse($this->paginatorFactory->canDisableOutputWalkers($queryBuilder));
    }

    private function getUseOutputWalkers(OffsetPaginator $paginator): mixed
    {
        $property = new \ReflectionProperty(OffsetPaginator::class, 'useOutputWalkers');

        return $property->getValue($paginator);
    }
}
