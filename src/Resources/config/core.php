<?php

declare(strict_types=1);

use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\DoctrineOrmProxyQueryFactory;
use Kreyu\Bundle\DataTableBundle\DataTableFactory;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryInterface;
use Kreyu\Bundle\DataTableBundle\DataTableRegistry;
use Kreyu\Bundle\DataTableBundle\DataTableRegistryInterface;
use Kreyu\Bundle\DataTableBundle\Maker\MakeDataTable;
use Kreyu\Bundle\DataTableBundle\Persistence\CachePersistenceClearer;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceClearerInterface;
use Kreyu\Bundle\DataTableBundle\Persistence\StaticPersistenceSubjectProvider;
use Kreyu\Bundle\DataTableBundle\Persistence\TokenStoragePersistenceSubjectProvider;
use Kreyu\Bundle\DataTableBundle\Query\ArrayProxyQueryFactory;
use Kreyu\Bundle\DataTableBundle\Request\HttpFoundationRequestHandler;
use Kreyu\Bundle\DataTableBundle\Type\DataTableType;
use Kreyu\Bundle\DataTableBundle\Type\ResolvedDataTableTypeFactory;
use Kreyu\Bundle\DataTableBundle\Type\ResolvedDataTableTypeFactoryInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\abstract_arg;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $configurator) {
    $services = $configurator->services();

    $services
        ->set('kreyu_data_table.resolved_type_factory', ResolvedDataTableTypeFactory::class)
        ->alias(ResolvedDataTableTypeFactoryInterface::class, 'kreyu_data_table.resolved_type_factory')
    ;

    $services
        ->set('kreyu_data_table.type.data_table', DataTableType::class)
        ->arg('$defaults', abstract_arg('Default options, provided by KreyuDataTableExtension and DefaultConfigurationPass'))
        ->tag('kreyu_data_table.type')
    ;

    $services
        ->set('kreyu_data_table.registry', DataTableRegistry::class)
        ->args([
            tagged_iterator('kreyu_data_table.type'),
            tagged_iterator('kreyu_data_table.type_extension'),
            tagged_iterator('kreyu_data_table.proxy_query.factory'),
            service('kreyu_data_table.resolved_type_factory'),
        ])
        ->alias(DataTableRegistryInterface::class, 'kreyu_data_table.registry')
    ;

    $services
        ->set('kreyu_data_table.factory', DataTableFactory::class)
        ->args([
            service('kreyu_data_table.registry'),
        ])
        ->alias(DataTableFactoryInterface::class, 'kreyu_data_table.factory')
    ;

    $services
        ->set('kreyu_data_table.persistence.subject_provider.token_storage', TokenStoragePersistenceSubjectProvider::class)
        ->args([service('security.token_storage')])
    ;

    $services
        ->set('kreyu_data_table.persistence.subject_provider.static', StaticPersistenceSubjectProvider::class)
    ;

    $services
        ->set('kreyu_data_table.request_handler.http_foundation', HttpFoundationRequestHandler::class)
    ;

    $services
        ->set('kreyu_data_table.proxy_query.factory.array', ArrayProxyQueryFactory::class)
        ->tag('kreyu_data_table.proxy_query.factory')
    ;

    $services
        ->set('kreyu_data_table.proxy_query.factory.doctrine_orm', DoctrineOrmProxyQueryFactory::class)
        ->tag('kreyu_data_table.proxy_query.factory')
    ;

    $services
        ->set('kreyu_data_table.persistence.clearer.cache', CachePersistenceClearer::class)
        ->tag('kreyu_data_table.persistence.clearer')
        ->args([service('kreyu_data_table.persistence.cache.default')])
        ->alias(PersistenceClearerInterface::class, 'kreyu_data_table.persistence.clearer.cache')
    ;

    $services
        ->set('kreyu_data_table.maker', MakeDataTable::class)
        ->tag('maker.command')
    ;
};
