<?php

declare(strict_types=1);

use Kreyu\Bundle\DataTableBundle\Factory\DataTableFactory;
use Kreyu\Bundle\DataTableBundle\Factory\DataTableFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Maker\MakeDataTable;
use Kreyu\Bundle\DataTableBundle\Type\DataTableTypeChain;
use Kreyu\Bundle\DataTableBundle\Type\DataTableTypeInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $configurator) {
    $services = $configurator->services();

    $services
        ->set('kreyu_data_table.type_chain', DataTableTypeChain::class)
        ->args([tagged_iterator('kreyu_data_table.type')]);

    $services
        ->set('kreyu_data_table.factory', DataTableFactory::class)
        ->args([
            service('kreyu_data_table.type_chain'),
            service('kreyu_data_table.column.mapper.factory'),
            service('kreyu_data_table.filter.mapper.factory'),
            service('form.factory'),
        ])
        ->alias(DataTableFactoryInterface::class, 'kreyu_data_table.factory');

    $services
        ->instanceof(DataTableTypeInterface::class)
        ->call('setFilterPersisterSubjectProvider', [service('kreyu_data_table.filter.persister_subject_provider.token_storage')]);

    $services
        ->set('kreyu_data_table.maker', MakeDataTable::class)
        ->tag('maker.command');
};
