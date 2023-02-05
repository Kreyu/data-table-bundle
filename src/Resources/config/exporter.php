<?php

declare(strict_types=1);

use Kreyu\Bundle\DataTableBundle\Bridge\PhpSpreadsheet\Exporter\Type\CsvType;
use Kreyu\Bundle\DataTableBundle\Bridge\PhpSpreadsheet\Exporter\Type\XlsType;
use Kreyu\Bundle\DataTableBundle\Bridge\PhpSpreadsheet\Exporter\Type\XlsxType;
use Kreyu\Bundle\DataTableBundle\Column\ColumnFactory;
use Kreyu\Bundle\DataTableBundle\Column\ColumnFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnRegistry;
use Kreyu\Bundle\DataTableBundle\Column\ColumnRegistryInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\ActionsType;
use Kreyu\Bundle\DataTableBundle\Column\Type\BooleanType;
use Kreyu\Bundle\DataTableBundle\Column\Type\CollectionType;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\LinkType;
use Kreyu\Bundle\DataTableBundle\Column\Type\NumberType;
use Kreyu\Bundle\DataTableBundle\Column\Type\ResolvedColumnTypeFactory;
use Kreyu\Bundle\DataTableBundle\Column\Type\ResolvedColumnTypeFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\TemplateType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextType;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterFactory;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterRegistry;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterRegistryInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ExporterType;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ResolvedExporterType;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ResolvedExporterTypeFactory;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ResolvedExporterTypeFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ResolvedExporterTypeInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $configurator) {
    $services = $configurator->services();

    $services
        ->set('kreyu_data_table.exporter.resolved_type_factory', ResolvedExporterTypeFactory::class)
        ->alias(ResolvedExporterTypeFactoryInterface::class, 'kreyu_data_table.exporting.resolved_type_factory')
    ;

    $services
        ->set('kreyu_data_table.exporter.registry', ExporterRegistry::class)
        ->args([
            tagged_iterator('kreyu_data_table.exporter.type'),
            service('kreyu_data_table.exporter.resolved_type_factory'),
        ])
        ->alias(ExporterRegistryInterface::class, 'kreyu_data_table.exporter.registry')
    ;

    $services
        ->set('kreyu_data_table.exporter.factory', ExporterFactory::class)
        ->args([service('kreyu_data_table.exporter.registry')])
        ->alias(ExporterFactoryInterface::class, 'kreyu_data_table.exporter.factory')
    ;

    $services
        ->set('kreyu_data_table.exporter.type.exporter', ExporterType::class)
        ->tag('kreyu_data_table.exporter.type')
    ;

    $services
        ->set('kreyu_data_table.exporter.type.phpspreadsheet_csv', CsvType::class)
        ->tag('kreyu_data_table.exporter.type')
    ;

    $services
        ->set('kreyu_data_table.exporter.type.phpspreadsheet_xls', XlsType::class)
        ->tag('kreyu_data_table.exporter.type')
    ;

    $services
        ->set('kreyu_data_table.exporter.type.phpspreadsheet_xlsx', XlsxType::class)
        ->tag('kreyu_data_table.exporter.type')
    ;
};
