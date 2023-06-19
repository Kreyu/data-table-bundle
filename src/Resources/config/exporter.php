<?php

declare(strict_types=1);

use Kreyu\Bundle\DataTableBundle\Bridge\OpenSpout\Exporter\Type as OpenSpout;
use Kreyu\Bundle\DataTableBundle\Bridge\PhpSpreadsheet\Exporter\Type\AbstractExporterType;
use Kreyu\Bundle\DataTableBundle\Bridge\PhpSpreadsheet\Exporter\Type\CsvExporterType;
use Kreyu\Bundle\DataTableBundle\Bridge\PhpSpreadsheet\Exporter\Type\HtmlExporterType;
use Kreyu\Bundle\DataTableBundle\Bridge\PhpSpreadsheet\Exporter\Type\OdsExporterType;
use Kreyu\Bundle\DataTableBundle\Bridge\PhpSpreadsheet\Exporter\Type\PdfExporterType;
use Kreyu\Bundle\DataTableBundle\Bridge\PhpSpreadsheet\Exporter\Type\PhpSpreadsheetExporterType;
use Kreyu\Bundle\DataTableBundle\Bridge\PhpSpreadsheet\Exporter\Type\XlsExporterType;
use Kreyu\Bundle\DataTableBundle\Bridge\PhpSpreadsheet\Exporter\Type\XlsxExporterType;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterFactory;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterRegistry;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterRegistryInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ExporterType;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ResolvedExporterTypeFactory;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ResolvedExporterTypeFactoryInterface;
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
        ->set('kreyu_data_table.exporter.type.open_spout.abstract', OpenSpout\AbstractExporterType::class)
        ->abstract()
        ->args([
            service('translator')->nullOnInvalid(),
        ])
    ;

    $services
        ->set('kreyu_data_table.exporter.type.open_spout.csv', OpenSpout\CsvExporterType::class)
        ->parent('kreyu_data_table.exporter.type.open_spout.abstract')
        ->tag('kreyu_data_table.exporter.type')
    ;

    $services
        ->set('kreyu_data_table.exporter.type.open_spout.xlsx', OpenSpout\XlsxExporterType::class)
        ->parent('kreyu_data_table.exporter.type.open_spout.abstract')
        ->tag('kreyu_data_table.exporter.type')
    ;

    $services
        ->set('kreyu_data_table.exporter.type.open_spout.ods', OpenSpout\OdsExporterType::class)
        ->parent('kreyu_data_table.exporter.type.open_spout.abstract')
        ->tag('kreyu_data_table.exporter.type')
    ;

    $services
        ->set(AbstractExporterType::class)
        ->abstract()
        ->args([
            service('translator')->nullOnInvalid(),
        ])
    ;

    $services
        ->set('kreyu_data_table.exporter.type.phpspreadsheet', PhpSpreadsheetExporterType::class)
        ->parent(AbstractExporterType::class)
        ->tag('kreyu_data_table.exporter.type')
    ;

    $services
        ->set('kreyu_data_table.exporter.type.phpspreadsheet_csv', CsvExporterType::class)
        ->parent(AbstractExporterType::class)
        ->tag('kreyu_data_table.exporter.type')
    ;

    $services
        ->set('kreyu_data_table.exporter.type.phpspreadsheet_xls', XlsExporterType::class)
        ->parent(AbstractExporterType::class)
        ->tag('kreyu_data_table.exporter.type')
    ;

    $services
        ->set('kreyu_data_table.exporter.type.phpspreadsheet_xlsx', XlsxExporterType::class)
        ->parent(AbstractExporterType::class)
        ->tag('kreyu_data_table.exporter.type')
    ;

    $services
        ->set('kreyu_data_table.exporter.type.phpspreadsheet_ods', OdsExporterType::class)
        ->parent(AbstractExporterType::class)
        ->tag('kreyu_data_table.exporter.type')
    ;

    $services
        ->set('kreyu_data_table.exporter.type.phpspreadsheet_html', HtmlExporterType::class)
        ->parent(AbstractExporterType::class)
        ->tag('kreyu_data_table.exporter.type')
    ;

    $services
        ->set('kreyu_data_table.exporter.type.phpspreadsheet_pdf', PdfExporterType::class)
        ->parent(AbstractExporterType::class)
        ->tag('kreyu_data_table.exporter.type')
    ;
};
