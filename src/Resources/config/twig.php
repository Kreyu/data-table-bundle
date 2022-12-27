<?php

declare(strict_types=1);

use Kreyu\Bundle\DataTableBundle\Bridge\Twig\DataTableExtension;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $configurator) {
    $services = $configurator->services();

    $services
        ->set('kreyu_data_table.twig.data_table_renderer_extension', DataTableExtension::class)
        ->args([
            service('kreyu_data_table.column.view.factory.column_header'),
            service('kreyu_data_table.column.view.factory.column_value'),
        ])
        ->tag('twig.extension');
};
