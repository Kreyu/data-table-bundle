<?php

declare(strict_types=1);

use Kreyu\Bundle\DataTableBundle\Action\Extension\DependencyInjection\DependencyInjectionActionExtension;
use Kreyu\Bundle\DataTableBundle\Exporter\Extension\DependencyInjection\DependencyInjectionExporterExtension;
use Kreyu\Bundle\DataTableBundle\Filter\Extension\DependencyInjection\DependencyInjectionFilterExtension;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\abstract_arg;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $configurator) {
    $configurator->services()
        ->set('kreyu_data_table.filter.extension', DependencyInjectionFilterExtension::class)
            ->args([
                abstract_arg('All services with tag "kreyu_data_table.filter.type" are stored in a service locator by DataTablePass'),
                abstract_arg('All services with tag "kreyu_data_table.filter.type_extension" are stored here by DataTablePass'),
            ])
            ->tag('kreyu_data_table.filter.extension', [
                'type' => 'kreyu_data_table.filter.type',
                'type_extension' => 'kreyu_data_table.filter.type_extension',
            ])

        ->set('kreyu_data_table.exporter.extension', DependencyInjectionExporterExtension::class)
            ->args([
                abstract_arg('All services with tag "kreyu_data_table.exporter.type" are stored in a service locator by DataTablePass'),
                abstract_arg('All services with tag "kreyu_data_table.exporter.type_extension" are stored here by DataTablePass'),
            ])
            ->tag('kreyu_data_table.exporter.extension', [
                'type' => 'kreyu_data_table.exporter.type',
                'type_extension' => 'kreyu_data_table.exporter.type_extension',
            ])

        ->set('kreyu_data_table.action.extension', DependencyInjectionActionExtension::class)
            ->args([
                abstract_arg('All services with tag "kreyu_data_table.action.type" are stored in a service locator by DataTablePass'),
                abstract_arg('All services with tag "kreyu_data_table.action.type_extension" are stored here by DataTablePass'),
            ])
            ->tag('kreyu_data_table.action.extension', [
                'type' => 'kreyu_data_table.action.type',
                'type_extension' => 'kreyu_data_table.action.type_extension',
            ])
    ;
};
