<?php

declare(strict_types=1);

use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\Factory\ProxyQueryFactory;
use Kreyu\Bundle\DataTableBundle\Factory\DataTableFactory;
use Kreyu\Bundle\DataTableBundle\Factory\DataTableFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Query\Factory\ProxyQueryFactoryChain;
use Kreyu\Bundle\DataTableBundle\Query\Factory\ProxyQueryFactoryChainInterface;
use Kreyu\Bundle\DataTableBundle\Type\DataTableTypeChain;
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
        ->arg('$dataTableTypeChain', service('kreyu_data_table.type_chain'))
        ->arg('$proxyQueryFactoryChain', service('kreyu_data_table.proxy_query.factory_chain'))
        ->arg('$columnMapperFactory', service('kreyu_data_table.column.mapper.factory'))
        ->arg('$filterMapperFactory', service('kreyu_data_table.filter.mapper.factory'))
        ->arg('$formFactory', service('form.factory'))
        ->alias(DataTableFactoryInterface::class, 'kreyu_data_table.factory');

    $services
        ->set('kreyu_data_table.proxy_query.factory_chain', ProxyQueryFactoryChain::class)
        ->args([tagged_iterator('kreyu_data_table.proxy_query_factory')])
        ->alias(ProxyQueryFactoryChainInterface::class, 'kreyu_data_table.query.factory_chain');

    $services
        ->set('kreyu_data_table.proxy_query.factory.doctrine.orm', ProxyQueryFactory::class)
        ->tag('kreyu_data_table.proxy_query_factory');
};
