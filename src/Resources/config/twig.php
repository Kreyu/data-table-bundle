<?php

declare(strict_types=1);

use Kreyu\Bundle\DataTableBundle\Bridge\Twig\ColumnRendererExtension;
use Kreyu\Bundle\DataTableBundle\Bridge\Twig\DataTableRendererExtension;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $configurator) {
    $services = $configurator->services();

    $services
        ->set('kreyu_data_table.twig.column_renderer_extension', ColumnRendererExtension::class)
        ->tag('twig.extension')
        ->args([service('kreyu_data_table.column.renderer.html')]);

    $services
        ->set('kreyu_data_table.twig.data_table_renderer_extension', DataTableRendererExtension::class)
        ->tag('twig.extension');
};
