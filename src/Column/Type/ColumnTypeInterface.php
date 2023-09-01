<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnHeaderView;
use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface ColumnTypeInterface
{
    public function buildColumn(ColumnBuilderInterface $builder, array $options): void;

    public function buildHeaderView(ColumnHeaderView $view, ColumnInterface $column, array $options): void;

    public function buildValueView(ColumnValueView $view, ColumnInterface $column, array $options): void;

    public function buildExportHeaderView(ColumnHeaderView $view, ColumnInterface $column, array $options): void;

    public function buildExportValueView(ColumnValueView $view, ColumnInterface $column, array $options): void;

    public function configureOptions(OptionsResolver $resolver): void;

    public function getBlockPrefix(): string;

    /**
     * @return class-string<ColumnTypeInterface>|null
     */
    public function getParent(): ?string;
}
