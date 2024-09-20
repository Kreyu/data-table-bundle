<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\DataCollector\Proxy;

use Kreyu\Bundle\DataTableBundle\DataCollector\DataTableDataCollectorInterface;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryInterface;
use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Type\DataTableTypeInterface;
use Kreyu\Bundle\DataTableBundle\Type\ResolvedDataTableTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResolvedDataTableTypeDataCollectorProxy implements ResolvedDataTableTypeInterface
{
    public function __construct(
        private ResolvedDataTableTypeInterface $proxiedType,
        private DataTableDataCollectorInterface $dataCollector,
    ) {
    }

    public function getName(): string
    {
        return $this->proxiedType->getName();
    }

    public function getParent(): ?ResolvedDataTableTypeInterface
    {
        return $this->proxiedType->getParent();
    }

    public function getInnerType(): DataTableTypeInterface
    {
        return $this->proxiedType->getInnerType();
    }

    public function getTypeExtensions(): array
    {
        return $this->proxiedType->getTypeExtensions();
    }

    public function createBuilder(DataTableFactoryInterface $factory, string $name, ?ProxyQueryInterface $query = null, array $options = []): DataTableBuilderInterface
    {
        $builder = $this->proxiedType->createBuilder($factory, $name, $query, $options);
        $builder->setAttribute('data_collector/passed_options', $options);
        $builder->setType($this);

        return $builder;
    }

    public function createView(DataTableInterface $dataTable): DataTableView
    {
        return $this->proxiedType->createView($dataTable);
    }

    public function createExportView(DataTableInterface $dataTable): DataTableView
    {
        return $this->proxiedType->createExportView($dataTable);
    }

    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $this->proxiedType->buildDataTable($builder, $options);
    }

    public function buildView(DataTableView $view, DataTableInterface $dataTable, array $options): void
    {
        $this->proxiedType->buildView($view, $dataTable, $options);

        $this->dataCollector->collectDataTableView($dataTable, $view);
    }

    public function buildExportView(DataTableView $view, DataTableInterface $dataTable, array $options): void
    {
        $this->proxiedType->buildExportView($view, $dataTable, $options);
    }

    public function getOptionsResolver(): OptionsResolver
    {
        return $this->proxiedType->getOptionsResolver();
    }
}
