<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnView;
use Kreyu\Bundle\DataTableBundle\Column\Extension\ColumnTypeExtensionInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;
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

    public function createView(ColumnInterface $column, DataTableView $parent = null): ColumnView;

    public function buildView(ColumnView $view, ColumnInterface $column, array $options): void;

    public function getOptionsResolver(): OptionsResolver;
}