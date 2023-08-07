<?php

declare(strict_types=1);

use Kreyu\Bundle\DataTableBundle\Extension\Core\DefaultConfigurationDataTableTypeExtension;
use Kreyu\Bundle\DataTableBundle\Extension\I18n\IntlMoneyColumnTypeExtension;
use Kreyu\Bundle\DataTableBundle\Extension\I18n\IntlNumberColumnTypeExtension;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator) {
    $services = $configurator->services();

    $services
        ->set('kreyu_data_table.type_extension.default_configuration', DefaultConfigurationDataTableTypeExtension::class)
        ->tag('kreyu_data_table.type_extension', ['priority' => 999])
    ;

    $services
        ->set('kreyu_data_table.column.type_extension.intl_number', IntlNumberColumnTypeExtension::class)
        ->tag('kreyu_data_table.column.type_extension', ['priority' => 999])
    ;

    $services
        ->set('kreyu_data_table.column.type_extension.intl_money', IntlMoneyColumnTypeExtension::class)
        ->tag('kreyu_data_table.column.type_extension', ['priority' => 999])
    ;
};
