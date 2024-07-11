<?php

declare(strict_types=1);

use Kreyu\Bundle\DataTableBundle\DataCollector\DataTableDataCollector;
use Kreyu\Bundle\DataTableBundle\DataCollector\DataTableDataExtractor;
use Kreyu\Bundle\DataTableBundle\DataCollector\Type\DataCollectorTypeExtension;
use Kreyu\Bundle\DataTableBundle\Debug\TraceableDataTableFactory;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set('kreyu_data_table.debug.factory', TraceableDataTableFactory::class)
            ->decorate('kreyu_data_table.factory')
            ->args([
                service('.inner'),
                service('kreyu_data_table.debug.data_collector'),
            ])

        ->set('kreyu_data_table.debug.data_collector', DataTableDataCollector::class)
            ->args([service('kreyu_data_table.debug.data_collector.extractor')])
            ->tag('data_collector')

        ->set('kreyu_data_table.debug.data_collector.extractor', DataTableDataExtractor::class)

        ->set('kreyu_data_table.debug.data_collector.type_extension', DataCollectorTypeExtension::class)
            ->args([service('kreyu_data_table.debug.data_collector')])
            ->tag('kreyu_data_table.type_extension')
    ;
};
