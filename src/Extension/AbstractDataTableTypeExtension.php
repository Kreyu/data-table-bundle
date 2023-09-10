<?php

namespace Kreyu\Bundle\DataTableBundle\Extension;

use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractDataTableTypeExtension implements DataTableTypeExtensionInterface
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
    }

    public function buildView(DataTableView $view, DataTableInterface $dataTable, array $options): void
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
    }
}
