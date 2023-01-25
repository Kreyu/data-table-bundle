<?php

declare(strict_types=1);

use Kreyu\Bundle\DataTableBundle\Extension\Core\ColumnFactoryExtension;
use Kreyu\Bundle\DataTableBundle\Extension\Core\FilterFactoryExtension;
use Kreyu\Bundle\DataTableBundle\Extension\Core\HttpFoundationExtension;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $configurator) {
    $services = $configurator->services();

    $services
        ->set('kreyu_data_table.type_extension.column_factory', ColumnFactoryExtension::class)
        ->args([service('kreyu_data_table.column.factory')])
        ->tag('kreyu_data_table.type_extension')
    ;

    $services
        ->set('kreyu_data_table.type_extension.filter_factory', FilterFactoryExtension::class)
        ->args([service('kreyu_data_table.filter.factory')])
        ->tag('kreyu_data_table.type_extension')
    ;

    $services
        ->set('kreyu_data_table.type_extension.http_foundation', HttpFoundationExtension::class)
        ->args([service('kreyu_data_table.request_handler.http_foundation')])
        ->tag('kreyu_data_table.type_extension')
    ;
};
