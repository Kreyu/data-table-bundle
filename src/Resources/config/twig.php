<?php

declare(strict_types=1);

use Kreyu\Bundle\DataTableBundle\Twig\DataTableExtension;
use Kreyu\Bundle\DataTableBundle\Twig\HelperExtension;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator) {
    $services = $configurator->services();

    $services
        ->set('kreyu_data_table.twig.data_table_extension', DataTableExtension::class)
        ->tag('twig.extension')
    ;

    $services
        ->set('kreyu_data_table.twig.helper_extension', HelperExtension::class)
        ->tag('twig.extension')
    ;
};
