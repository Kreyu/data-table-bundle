<?php

declare(strict_types=1);

use Kreyu\Bundle\DataTableBundle\Extension\DependencyInjection\DependencyInjectionDataTableExtension;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\abstract_arg;

return static function (ContainerConfigurator $configurator) {
    $services = $configurator->services();

    $services
        ->set('kreyu_data_table.extension', DependencyInjectionDataTableExtension::class)
        ->args([
            abstract_arg('All services with tag "kreyu_data_table.type" are stored in a service locator by DataTablePass'),
            abstract_arg('All services with tag "kreyu_data_table.type_extension" are stored here by DataTablePass'),
        ])
        ->tag('kreyu_data_table.extension')
    ;
};
