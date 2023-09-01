<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Extension;

use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Type\DataTableTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface DataTableTypeExtensionInterface
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void;

    public function buildView(DataTableView $view, DataTableInterface $dataTable, array $options): void;

    public function configureOptions(OptionsResolver $resolver): void;

    /**
     * @return iterable<class-string<DataTableTypeInterface>>
     */
    public static function getExtendedTypes(): iterable;
}
