<?php

declare(strict_types=1);

use Kreyu\Bundle\DataTableBundle\DataCollector\DataTableDataCollector;
use Kreyu\Bundle\DataTableBundle\DataCollector\DataTableDataExtractor;
use Kreyu\Bundle\DataTableBundle\DataCollector\Proxy\ResolvedActionTypeFactoryDataCollectorProxy;
use Kreyu\Bundle\DataTableBundle\DataCollector\Proxy\ResolvedColumnTypeFactoryDataCollectorProxy;
use Kreyu\Bundle\DataTableBundle\DataCollector\Proxy\ResolvedDataTableTypeFactoryDataCollectorProxy;
use Kreyu\Bundle\DataTableBundle\DataCollector\Proxy\ResolvedExporterTypeFactoryDataCollectorProxy;
use Kreyu\Bundle\DataTableBundle\DataCollector\Proxy\ResolvedFilterTypeFactoryDataCollectorProxy;
use Kreyu\Bundle\DataTableBundle\DataCollector\Type\DataCollectorTypeExtension;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set('kreyu_data_table.debug.data_collector', DataTableDataCollector::class)
            ->args([service('kreyu_data_table.debug.data_collector.extractor')])
            ->tag('data_collector')

        ->set('kreyu_data_table.debug.data_collector.extractor', DataTableDataExtractor::class)

        ->set('kreyu_data_table.debug.data_collector.type_extension', DataCollectorTypeExtension::class)
            ->args([service('kreyu_data_table.debug.data_collector')])
            ->tag('kreyu_data_table.type_extension')

        ->set('kreyu_data_table.debug.resolved_type_factory', ResolvedDataTableTypeFactoryDataCollectorProxy::class)
            ->decorate('kreyu_data_table.resolved_type_factory')
            ->args([
                service('.inner'),
                service('kreyu_data_table.debug.data_collector'),
            ])

        ->set('kreyu_data_table.debug.column.resolved_type_factory', ResolvedColumnTypeFactoryDataCollectorProxy::class)
            ->decorate('kreyu_data_table.column.resolved_type_factory')
            ->args([
                service('.inner'),
                service('kreyu_data_table.debug.data_collector'),
            ])

        ->set('kreyu_data_table.debug.filter.resolved_type_factory', ResolvedFilterTypeFactoryDataCollectorProxy::class)
            ->decorate('kreyu_data_table.filter.resolved_type_factory')
            ->args([
                service('.inner'),
                service('kreyu_data_table.debug.data_collector'),
            ])

        ->set('kreyu_data_table.debug.action.resolved_type_factory', ResolvedActionTypeFactoryDataCollectorProxy::class)
            ->decorate('kreyu_data_table.action.resolved_type_factory')
            ->args([
                service('.inner'),
                service('kreyu_data_table.debug.data_collector'),
            ])

        ->set('kreyu_data_table.debug.exporter.resolved_type_factory', ResolvedExporterTypeFactoryDataCollectorProxy::class)
            ->decorate('kreyu_data_table.exporter.resolved_type_factory')
            ->args([
                service('.inner'),
            ])
    ;
};
