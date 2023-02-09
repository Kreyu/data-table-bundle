<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Type;

use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface DataTableTypeInterface
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void;

    public function buildView(DataTableView $view, DataTableInterface $dataTable, array $options): void;

    public function configureOptions(OptionsResolver $resolver): void;

    public function getName(): string;

    /**
     * @return class-string<DataTableTypeInterface>|null
     */
    public function getParent(): ?string;
}
