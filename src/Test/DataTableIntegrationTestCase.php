<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Test;

use Kreyu\Bundle\DataTableBundle\Column\ColumnFactory;
use Kreyu\Bundle\DataTableBundle\Column\ColumnFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnRegistry;
use Kreyu\Bundle\DataTableBundle\Column\ColumnRegistryInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\ResolvedColumnTypeFactory;
use Kreyu\Bundle\DataTableBundle\Column\Type\ResolvedColumnTypeFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\DataTableFactory;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryInterface;
use Kreyu\Bundle\DataTableBundle\DataTableRegistry;
use Kreyu\Bundle\DataTableBundle\Query\ArrayProxyQueryFactory;
use Kreyu\Bundle\DataTableBundle\Type\DataTableType;
use Kreyu\Bundle\DataTableBundle\Type\DataTableTypeInterface;
use Kreyu\Bundle\DataTableBundle\Type\ResolvedDataTableTypeFactory;
use Kreyu\Bundle\DataTableBundle\Type\ResolvedDataTableTypeFactoryInterface;
use PHPUnit\Framework\TestCase;

abstract class DataTableIntegrationTestCase extends TestCase
{
    protected DataTableFactoryInterface $dataTableFactory;

    protected function setUp(): void
    {
        $this->dataTableFactory = $this->createDataTableFactory();
    }

    protected function createDataTableFactory(): DataTableFactoryInterface
    {
        return new DataTableFactory($this->createDataTableRegistry());
    }

    protected function createDataTableRegistry(): DataTableRegistry
    {
        return new DataTableRegistry(
            types: $this->getDataTableTypes(),
            typeExtensions: $this->getDataTableTypeExtensions(),
            proxyQueryFactories: $this->getProxyQueryFactories(),
            resolvedTypeFactory: $this->getResolvedDataTableTypeFactory(),
        );
    }

    /**
     * @return DataTableTypeInterface[]
     */
    protected function getDataTableTypes(): array
    {
        return [
            new DataTableType([
                'column_factory' => $this->createColumnFactory(),
            ]),
        ];
    }

    protected function getDataTableTypeExtensions(): array
    {
        return [];
    }

    protected function getProxyQueryFactories(): array
    {
        return [
            new ArrayProxyQueryFactory(),
        ];
    }

    protected function getResolvedDataTableTypeFactory(): ResolvedDataTableTypeFactoryInterface
    {
        return new ResolvedDataTableTypeFactory();
    }

    protected function createColumnFactory(): ColumnFactoryInterface
    {
        return new ColumnFactory($this->createColumnRegistry());
    }

    protected function createColumnRegistry(): ColumnRegistryInterface
    {
        return new ColumnRegistry(
            types: $this->getColumnTypes(),
            typeExtensions: $this->getColumnTypeExtensions(),
            resolvedTypeFactory: $this->getResolvedColumnTypeFactory(),
        );
    }

    protected function getColumnTypes(): array
    {
        return [
            new ColumnType(),
            new TextColumnType(),
        ];
    }

    protected function getColumnTypeExtensions(): array
    {
        return [];
    }

    protected function getResolvedColumnTypeFactory(): ResolvedColumnTypeFactoryInterface
    {
        return new ResolvedColumnTypeFactory();
    }
}
