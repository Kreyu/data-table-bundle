<?php

declare(strict_types=1);

use Kreyu\Bundle\DataTableBundle\Extension\Core\DefaultConfigurationExtension;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator) {
    $services = $configurator->services();

    $services
        ->set('kreyu_data_table.type_extension.default_configuration', DefaultConfigurationExtension::class)
        ->tag('kreyu_data_table.type_extension')
    ;
};
