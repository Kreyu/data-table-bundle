<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator) {
    $services = $configurator->services();

    $services
        ->set('kreyu_data_table.personalization.persistence.adapter.cache')
        ->parent('kreyu_data_table.persistence.adapter.cache')
        ->arg('$prefix', 'personalization')
        ->tag('kreyu_data_table.persistence.adapter')
        ->tag('kreyu_data_table.personalization.persistence.adapter')
    ;
};
