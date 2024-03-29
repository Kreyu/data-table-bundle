<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Type;

use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface DataTableTypeInterface
{
    /**
     * @param array<string, mixed> $options
     */
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void;

    /**
     * @param array<string, mixed> $options
     */
    public function buildView(DataTableView $view, DataTableInterface $dataTable, array $options): void;

    /**
     * @param array<string, mixed> $options
     */
    public function buildExportView(DataTableView $view, DataTableInterface $dataTable, array $options): void;

    public function configureOptions(OptionsResolver $resolver): void;

    public function getName(): string;

    /**
     * @return class-string<DataTableTypeInterface>|null
     */
    public function getParent(): ?string;
}
