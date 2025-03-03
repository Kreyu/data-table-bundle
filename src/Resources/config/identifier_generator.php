<?php

declare(strict_types=1);

use Kreyu\Bundle\DataTableBundle\IdentifierGenerator\DataTableTurboIdentifierGenerator;
use Kreyu\Bundle\DataTableBundle\IdentifierGenerator\DataTableTurboIdentifierGeneratorInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator) {
    $services = $configurator->services();

    $services
        ->set('kreyu_data_table.identifier_generator.datatable_turbo', DataTableTurboIdentifierGenerator::class)
        ->alias(DataTableTurboIdentifierGeneratorInterface::class, 'kreyu_data_table.identifier_generator.datatable_turbo')
    ;
};
