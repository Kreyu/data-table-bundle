<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Test\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnRegistry;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnTypeInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\ResolvedColumnTypeFactory;
use Kreyu\Bundle\DataTableBundle\DataTableFactory;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryInterface;
use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\DataTableRegistry;
use Kreyu\Bundle\DataTableBundle\DataTableRegistryInterface;
use Kreyu\Bundle\DataTableBundle\Query\ArrayProxyQuery;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Column\TestColumnFactory;
use Kreyu\Bundle\DataTableBundle\Type\DataTableType;
use Kreyu\Bundle\DataTableBundle\Type\ResolvedDataTableTypeFactory;
use PHPUnit\Framework\TestCase;

abstract class ColumnTypeTestCase extends TestCase
{
    protected ColumnFactoryInterface $factory;
    protected DataTableInterface $dataTable;

    protected function setUp(): void
    {
        parent::setUp();

        $this->recreateFactoryWithType($this->instantiateType());
    }

    protected function recreateFactoryWithType(ColumnTypeInterface $type): void
    {
        $registry = new ColumnRegistry(
            types: [$type],
            typeExtensions: [],
            resolvedTypeFactory: new ResolvedColumnTypeFactory(),
        );

        $this->factory = new TestColumnFactory($registry);
        $this->factory->setDataTable($this->getDataTable());
    }

    protected function instantiateType(): ColumnTypeInterface
    {
        return new ($this->getTestedType());
    }

    abstract protected function getTestedType(): string;

    protected function createDataTableRegistry(): DataTableRegistryInterface
    {
        return new DataTableRegistry(
            types: [new DataTableType()],
            typeExtensions: [],
            proxyQueryFactories: [],
            resolvedTypeFactory: new ResolvedDataTableTypeFactory(),
        );
    }

    protected function createDataTableFactory(): DataTableFactoryInterface
    {
        return new DataTableFactory($this->createDataTableRegistry());
    }

    protected function getDataTable(): DataTableInterface
    {
        return $this->dataTable ??= $this->createDataTableFactory()->create(DataTableType::class, new ArrayProxyQuery([]));
    }
}