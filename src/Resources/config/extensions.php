<?php

declare(strict_types=1);

use Kreyu\Bundle\DataTableBundle\Exporter\Extension\DependencyInjection\DependencyInjectionExporterExtension;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\abstract_arg;

return static function (ContainerConfigurator $configurator) {
    $configurator->services()
        ->set('kreyu_data_table.exporter.extension', DependencyInjectionExporterExtension::class)
            ->args([
                abstract_arg('All services with tag "kreyu_data_table.exporter.type" are stored in a service locator by DataTablePass'),
                abstract_arg('All services with tag "kreyu_data_table.exporter.type_extension" are stored here by DataTablePass'),
            ])
            ->tag('kreyu_data_table.exporter.extension', [
                'type' => 'kreyu_data_table.exporter.type',
                'type_extension' => 'kreyu_data_table.exporter.type_extension',
            ])
    ;
};
