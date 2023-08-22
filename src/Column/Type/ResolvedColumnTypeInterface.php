<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnHeaderView;
use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Kreyu\Bundle\DataTableBundle\Column\Extension\ColumnTypeExtensionInterface;
use Kreyu\Bundle\DataTableBundle\HeaderRowView;
use Kreyu\Bundle\DataTableBundle\ValueRowView;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface ResolvedColumnTypeInterface
{
    public function getBlockPrefix(): string;

    public function getParent(): ?ResolvedColumnTypeInterface;

    public function getInnerType(): ColumnTypeInterface;

    /**
     * @return array<ColumnTypeExtensionInterface>
     */
    public function getTypeExtensions(): array;

    public function createBuilder(ColumnFactoryInterface $factory, string $name, array $options = []): ColumnBuilderInterface;

    public function createHeaderView(ColumnInterface $column, HeaderRowView $parent = null): ColumnHeaderView;

    public function createValueView(ColumnInterface $column, ValueRowView $parent = null): ColumnValueView;

    public function createExportHeaderView(ColumnInterface $column, HeaderRowView $parent = null): ColumnHeaderView;

    public function createExportValueView(ColumnInterface $column, ValueRowView $parent = null): ColumnValueView;

    public function buildColumn(ColumnBuilderInterface $builder, array $options): void;

    public function buildHeaderView(ColumnHeaderView $view, ColumnInterface $column, array $options): void;

    public function buildValueView(ColumnValueView $view, ColumnInterface $column, array $options): void;

    public function buildExportHeaderView(ColumnHeaderView $view, ColumnInterface $column, array $options): void;

    public function buildExportValueView(ColumnValueView $view, ColumnInterface $column, array $options): void;

    public function getOptionsResolver(): OptionsResolver;
}
