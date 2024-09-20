<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\DataCollector\Proxy;

use Kreyu\Bundle\DataTableBundle\Column\ColumnBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnHeaderView;
use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnTypeInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\ResolvedColumnTypeInterface;
use Kreyu\Bundle\DataTableBundle\DataCollector\DataTableDataCollectorInterface;
use Kreyu\Bundle\DataTableBundle\HeaderRowView;
use Kreyu\Bundle\DataTableBundle\ValueRowView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResolvedColumnTypeDataCollectorProxy implements ResolvedColumnTypeInterface
{
    public function __construct(
        private ResolvedColumnTypeInterface $proxiedType,
        private DataTableDataCollectorInterface $dataCollector,
    ) {
    }

    public function getBlockPrefix(): string
    {
        return $this->proxiedType->getBlockPrefix();
    }

    public function getParent(): ?ResolvedColumnTypeInterface
    {
        return $this->proxiedType->getParent();
    }

    public function getInnerType(): ColumnTypeInterface
    {
        return $this->proxiedType->getInnerType();
    }

    public function getTypeExtensions(): array
    {
        return $this->proxiedType->getTypeExtensions();
    }

    public function createBuilder(ColumnFactoryInterface $factory, string $name, array $options): ColumnBuilderInterface
    {
        $builder = $this->proxiedType->createBuilder($factory, $name, $options);
        $builder->setAttribute('data_collector/passed_options', $options);
        $builder->setType($this);

        return $builder;
    }

    public function createHeaderView(ColumnInterface $column, ?HeaderRowView $parent = null): ColumnHeaderView
    {
        return $this->proxiedType->createHeaderView($column, $parent);
    }

    public function createValueView(ColumnInterface $column, ?ValueRowView $parent = null): ColumnValueView
    {
        return $this->proxiedType->createValueView($column, $parent);
    }

    public function createExportHeaderView(ColumnInterface $column, ?HeaderRowView $parent = null): ColumnHeaderView
    {
        return $this->proxiedType->createExportHeaderView($column, $parent);
    }

    public function createExportValueView(ColumnInterface $column, ?ValueRowView $parent = null): ColumnValueView
    {
        return $this->proxiedType->createExportValueView($column, $parent);
    }

    public function buildColumn(ColumnBuilderInterface $builder, array $options): void
    {
        $this->proxiedType->buildColumn($builder, $options);
    }

    public function buildHeaderView(ColumnHeaderView $view, ColumnInterface $column, array $options): void
    {
        $this->proxiedType->buildHeaderView($view, $column, $options);
        $this->dataCollector->collectColumnHeaderView($column, $view);
    }

    public function buildValueView(ColumnValueView $view, ColumnInterface $column, array $options): void
    {
        $this->proxiedType->buildValueView($view, $column, $options);
        $this->dataCollector->collectColumnValueView($column, $view);
    }

    public function buildExportHeaderView(ColumnHeaderView $view, ColumnInterface $column, array $options): void
    {
        $this->proxiedType->buildExportHeaderView($view, $column, $options);
    }

    public function buildExportValueView(ColumnValueView $view, ColumnInterface $column, array $options): void
    {
        $this->proxiedType->buildExportValueView($view, $column, $options);
    }

    public function getOptionsResolver(): OptionsResolver
    {
        return $this->proxiedType->getOptionsResolver();
    }
}
