<?php

declare(strict_types=1);

use Kreyu\Bundle\DataTableBundle\Twig\DataTableExtension;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $configurator) {
    $services = $configurator->services();

    $services
        ->set('kreyu_data_table.twig.data_table_extension', DataTableExtension::class)
        ->tag('twig.extension')
        ->args([
            service('kreyu_data_table.column.column_sort_url_generator'),
            service('kreyu_data_table.filter.filter_clear_url_generator'),
        ])
    ;
};
