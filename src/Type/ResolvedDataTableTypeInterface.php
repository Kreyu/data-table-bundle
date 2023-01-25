<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Type;

use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryInterface;
use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Extension\DataTableTypeExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface ResolvedDataTableTypeInterface
{
    public function getName(): string;

    public function getParent(): ?ResolvedDataTableTypeInterface;

    public function getInnerType(): DataTableTypeInterface;

    /**
     * @return array<DataTableTypeExtensionInterface>
     */
    public function getTypeExtensions(): array;

    public function createBuilder(DataTableFactoryInterface $factory, string $name, ?ProxyQueryInterface $query = null, array $options = []): DataTableBuilderInterface;

    public function createView(DataTableInterface $dataTable): DataTableView;

    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void;

    public function buildView(DataTableView $view, DataTableInterface $dataTable, array $options): void;

    public function getOptionsResolver(): OptionsResolver;
}
