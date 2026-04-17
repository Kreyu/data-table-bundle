<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit;

use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\DataTableFactory;
use Kreyu\Bundle\DataTableBundle\DataTableRegistry;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\CustomQuery;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\DataTable\Query\CustomProxyQuery;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\DataTable\Query\CustomProxyQueryFactory;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\DataTable\Type\ConfigurableDataTableType;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\DataTable\Type\CreateQueryChildDataTableType;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\DataTable\Type\CreateQueryDataTableType;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\DataTable\Type\SimpleDataTableType;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;
use Kreyu\Bundle\DataTableBundle\Type\DataTableType;
use Kreyu\Bundle\DataTableBundle\Type\ResolvedDataTableTypeFactory;
use PHPUnit\Framework\TestCase;

class DataTableFactoryTest extends TestCase
{
    public function testCreateNamedBuilder()
    {
        $builder = $this->createFactory()->createNamedBuilder('name', ConfigurableDataTableType::class, options: [
            'foo' => 'a',
            'bar' => 'b',
        ]);

        $this->assertSame('a', $builder->getOption('foo'));
        $this->assertSame('b', $builder->getOption('bar'));
    }

    public function testCreateNamedBuilderUsesProxyQueryFactory()
    {
        $proxyQueryFactory = new CustomProxyQueryFactory();

        $builder = $this->createFactory(proxyQueryFactories: [$proxyQueryFactory])
            ->createNamedBuilder('name', data: new CustomQuery());

        $this->assertInstanceOf(CustomProxyQuery::class, $builder->getQuery());
    }

    public function testCreateNamedBuilderWithProxyQueryData()
    {
        $data = new CustomProxyQuery();

        $builder = $this->createFactory()->createNamedBuilder('name', data: $data);

        $this->assertSame($data, $builder->getQuery());
    }

    public function testCreateNamedBuilderUsesTypeCreateQuery()
    {
        $builder = $this->createFactory(proxyQueryFactories: [new CustomProxyQueryFactory()])
            ->createNamedBuilder('name', CreateQueryDataTableType::class);

        $this->assertInstanceOf(CustomProxyQuery::class, $builder->getQuery());
    }

    public function testCreateNamedBuilderDataOverridesCreateQuery()
    {
        $explicitData = new CustomProxyQuery();

        $builder = $this->createFactory(proxyQueryFactories: [new CustomProxyQueryFactory()])
            ->createNamedBuilder('name', CreateQueryDataTableType::class, data: $explicitData);

        $this->assertSame($explicitData, $builder->getQuery());
    }

    public function testCreateQueryChainFallsBackToParent()
    {
        $builder = $this->createFactory(
            types: [
                new DataTableType(),
                new CreateQueryDataTableType(),
                new CreateQueryChildDataTableType(),
            ],
            proxyQueryFactories: [new CustomProxyQueryFactory()],
        )->createNamedBuilder('name', CreateQueryChildDataTableType::class);

        $this->assertInstanceOf(CustomProxyQuery::class, $builder->getQuery());
    }

    public function testBuildDataTableSeesQueryFromCreateQuery()
    {
        $spy = new \stdClass();
        $spy->seen = null;

        $type = new class($spy) extends AbstractDataTableType {
            public function __construct(
                private \stdClass $spy,
            ) {
            }

            public function createQuery(array $options): mixed
            {
                return new CustomQuery();
            }

            public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
            {
                $this->spy->seen = $builder->getQuery();
            }
        };

        $this->createFactory(
            types: [new DataTableType(), $type],
            proxyQueryFactories: [new CustomProxyQueryFactory()],
        )->createNamedBuilder('name', $type::class);

        $this->assertInstanceOf(CustomProxyQuery::class, $spy->seen);
    }

    public function testCreateBuilderUsesDataTableName()
    {
        $builder = $this->createFactory()->createBuilder(SimpleDataTableType::class);

        $this->assertSame('simple', $builder->getName());
    }

    public function testCreate()
    {
        $dataTable = $this->createFactory()->create(
            type: ConfigurableDataTableType::class,
            data: new CustomProxyQuery(),
            options: [
                'foo' => 'a',
                'bar' => 'b',
            ],
        );

        $this->assertSame('configurable', $dataTable->getName());
        $this->assertSame('a', $dataTable->getConfig()->getOption('foo'));
        $this->assertSame('b', $dataTable->getConfig()->getOption('bar'));
        $this->assertInstanceOf(ConfigurableDataTableType::class, $dataTable->getConfig()->getType()->getInnerType());
    }

    public function testCreateNamed()
    {
        $dataTable = $this->createFactory()->createNamed(
            name: 'name',
            type: ConfigurableDataTableType::class,
            data: new CustomProxyQuery(),
            options: [
                'foo' => 'a',
                'bar' => 'b',
            ],
        );

        $this->assertSame('name', $dataTable->getName());
        $this->assertSame('a', $dataTable->getConfig()->getOption('foo'));
        $this->assertSame('b', $dataTable->getConfig()->getOption('bar'));
        $this->assertInstanceOf(ConfigurableDataTableType::class, $dataTable->getConfig()->getType()->getInnerType());
    }

    private function createFactory(?array $types = null, array $proxyQueryFactories = []): DataTableFactory
    {
        $registry = new DataTableRegistry(
            types: $types ?? [
                new DataTableType(),
                new SimpleDataTableType(),
                new ConfigurableDataTableType(),
                new CreateQueryDataTableType(),
                new CreateQueryChildDataTableType(),
            ],
            typeExtensions: [],
            proxyQueryFactories: $proxyQueryFactories,
            resolvedTypeFactory: new ResolvedDataTableTypeFactory(),
        );

        return new DataTableFactory($registry);
    }
}
