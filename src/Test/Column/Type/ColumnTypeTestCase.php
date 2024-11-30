<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Test\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnRegistry;
use Kreyu\Bundle\DataTableBundle\Column\ColumnRegistryInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnTypeInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\ResolvedColumnTypeFactory;
use Kreyu\Bundle\DataTableBundle\Column\Type\ResolvedColumnTypeFactoryInterface;
use Kreyu\Bundle\DataTableBundle\DataTableFactory;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryInterface;
use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\DataTableRegistry;
use Kreyu\Bundle\DataTableBundle\DataTableRegistryInterface;
use Kreyu\Bundle\DataTableBundle\Query\ArrayProxyQuery;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Column\TestColumnFactory;
use Kreyu\Bundle\DataTableBundle\Type\DataTableType;
use Kreyu\Bundle\DataTableBundle\Type\ResolvedDataTableTypeFactory;
use Kreyu\Bundle\DataTableBundle\Type\ResolvedDataTableTypeFactoryInterface;
use PHPUnit\Framework\TestCase;

abstract class ColumnTypeTestCase extends TestCase
{
    protected ColumnFactoryInterface $columnFactory;
    protected ColumnRegistryInterface $columnRegistry;
    protected ResolvedColumnTypeFactoryInterface $resolvedColumnTypeFactory;
    protected DataTableFactoryInterface $dataTableFactory;
    protected DataTableRegistryInterface $dataTableRegistry;
    protected ResolvedDataTableTypeFactoryInterface $resolvedDataTableTypeFactory;
    protected DataTableInterface $dataTable;

    abstract protected function getTestedColumnType(): ColumnTypeInterface;

    protected function createColumn(array $options = []): ColumnInterface
    {
        return $this->getColumnFactory()->create($this->getTestedColumnType()::class, $options);
    }

    protected function createNamedColumn(string $name, array $options = []): ColumnInterface
    {
        return $this->getColumnFactory()->createNamed($name, $this->getTestedColumnType()::class, $options);
    }

    protected function getColumnFactory(): ColumnFactoryInterface
    {
        return $this->columnFactory ??= $this->createColumnFactory();
    }

    protected function createColumnFactory(): ColumnFactoryInterface
    {
        $factory = new TestColumnFactory($this->getColumnRegistry());
        $factory->setDataTable($this->getDataTable());

        return $factory;
    }

    protected function getColumnRegistry(): ColumnRegistryInterface
    {
        return $this->columnRegistry ??= $this->createColumnRegistry();
    }

    protected function createColumnRegistry(): ColumnRegistryInterface
    {
        return new ColumnRegistry(
            types: [$this->getTestedColumnType()],
            typeExtensions: [],
            resolvedTypeFactory: $this->getResolvedColumnTypeFactory(),
        );
    }

    protected function getResolvedColumnTypeFactory(): ResolvedColumnTypeFactoryInterface
    {
        return $this->resolvedColumnTypeFactory ??= $this->createResolvedColumnTypeFactory();
    }

    protected function createResolvedColumnTypeFactory(): ResolvedColumnTypeFactoryInterface
    {
        return new ResolvedColumnTypeFactory();
    }

    protected function getDataTableRegistry(): DataTableRegistryInterface
    {
        return $this->dataTableRegistry ??= $this->createDataTableRegistry();
    }

    protected function createDataTableRegistry(): DataTableRegistryInterface
    {
        return new DataTableRegistry(
            types: [new DataTableType()],
            typeExtensions: [],
            proxyQueryFactories: [],
            resolvedTypeFactory: $this->getResolvedDataTableTypeFactory(),
        );
    }

    protected function getDataTableFactory(): DataTableFactoryInterface
    {
        return $this->dataTableFactory ??= $this->createDataTableFactory();
    }

    protected function createDataTableFactory(): DataTableFactoryInterface
    {
        return new DataTableFactory($this->createDataTableRegistry());
    }

    protected function getResolvedDataTableTypeFactory(): ResolvedDataTableTypeFactoryInterface
    {
        return $this->resolvedDataTableTypeFactory ??= $this->createResolvedDataTableTypeFactory();
    }

    protected function createResolvedDataTableTypeFactory(): ResolvedDataTableTypeFactoryInterface
    {
        return new ResolvedDataTableTypeFactory();
    }

    protected function getDataTable(): DataTableInterface
    {
        return $this->dataTable ??= $this->createDataTable();
    }

    protected function createDataTable(): DataTableInterface
    {
        return $this->getDataTableFactory()->create(DataTableType::class, new ArrayProxyQuery([]));
    }
}
