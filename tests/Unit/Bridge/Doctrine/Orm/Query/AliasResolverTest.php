<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Bridge\Doctrine\Orm\Query;

use Doctrine\ORM\QueryBuilder;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\AliasResolver;
use Kreyu\Bundle\DataTableBundle\Tests\Unit\Bridge\Doctrine\Orm\Fixtures\Entity\Product;
use Kreyu\Bundle\DataTableBundle\Tests\Unit\Bridge\Doctrine\Orm\Fixtures\TestEntityManagerFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class AliasResolverTest extends TestCase
{
    private AliasResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new AliasResolver();
    }

    #[DataProvider('provideResolveCases')]
    public function testResolve(QueryBuilder $queryBuilder, string $queryPath, string $resolvedQueryPath): void
    {
        $this->assertEquals($resolvedQueryPath, $this->resolver->resolve($queryPath, $queryBuilder));
    }

    public static function provideResolveCases(): iterable
    {
        yield 'Without alias in query path' => [
            TestEntityManagerFactory::create()
                ->createQueryBuilder()
                ->from(Product::class, 'product'),
            'name',
            'product.name',
        ];

        yield 'With alias in query path' => [
            TestEntityManagerFactory::create()
                ->createQueryBuilder()
                ->from(Product::class, 'product')
                ->leftJoin('product.category', 'category'),
            'category.name',
            'category.name',
        ];

        yield 'With query path present in SELECT part' => [
            TestEntityManagerFactory::create()
                ->createQueryBuilder()
                ->addSelect('UPPER(product.name) AS product_name')
                ->from(Product::class, 'product')
                ->leftJoin('product.category', 'category'),
            'product_name',
            'product_name',
        ];

        yield 'With query path present in SELECT part marked as HIDDEN' => [
            TestEntityManagerFactory::create()
                ->createQueryBuilder()
                ->addSelect('UPPER(product.name) AS HIDDEN product_name')
                ->from(Product::class, 'product')
                ->leftJoin('product.category', 'category'),
            'product_name',
            'product_name',
        ];
    }
}
