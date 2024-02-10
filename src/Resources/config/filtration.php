<?php

declare(strict_types=1);

use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\BooleanFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\CallbackFilterType as DoctrineOrmCallbackFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\DateFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\DateRangeFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\DateTimeFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\DoctrineOrmFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\EntityFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\NumericFilterType;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type\StringFilterType;
use Kreyu\Bundle\DataTableBundle\Filter\FilterClearUrlGenerator;
use Kreyu\Bundle\DataTableBundle\Filter\FilterClearUrlGeneratorInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterFactory;
use Kreyu\Bundle\DataTableBundle\Filter\FilterFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterRegistry;
use Kreyu\Bundle\DataTableBundle\Filter\FilterRegistryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Type\CallbackFilterType;
use Kreyu\Bundle\DataTableBundle\Filter\Type\FilterType;
use Kreyu\Bundle\DataTableBundle\Filter\Type\ResolvedFilterTypeFactory;
use Kreyu\Bundle\DataTableBundle\Filter\Type\ResolvedFilterTypeFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Type\SearchFilterType;
use Kreyu\Bundle\DataTableBundle\Persistence\CachePersistenceAdapter;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $configurator) {
    $services = $configurator->services();

    $services
        ->set('kreyu_data_table.persistence.adapter.cache', CachePersistenceAdapter::class)
        ->args([service('kreyu_data_table.persistence.cache.default')])
        ->abstract()
    ;

    $services
        ->set('kreyu_data_table.filtration.persistence.adapter.cache')
        ->parent('kreyu_data_table.persistence.adapter.cache')
        ->arg('$prefix', 'filtration')
        ->tag('kreyu_data_table.persistence.adapter')
        ->tag('kreyu_data_table.filtration.persistence.adapter')
    ;

    $services
        ->set('kreyu_data_table.filter.resolved_type_factory', ResolvedFilterTypeFactory::class)
        ->alias(ResolvedFilterTypeFactoryInterface::class, 'kreyu_data_table.resolved_type_factory')
    ;

    $services
        ->set('kreyu_data_table.filter.registry', FilterRegistry::class)
        ->args([
            tagged_iterator('kreyu_data_table.filter.type'),
            tagged_iterator('kreyu_data_table.filter.type_extension'),
            service('kreyu_data_table.filter.resolved_type_factory'),
        ])
        ->alias(FilterRegistryInterface::class, 'kreyu_data_table.filter.registry')
    ;

    $services
        ->set('kreyu_data_table.filter.factory', FilterFactory::class)
        ->args([service('kreyu_data_table.filter.registry')])
        ->alias(FilterFactoryInterface::class, 'kreyu_data_table.filter.factory')
    ;

    $services
        ->set('kreyu_data_table.filter.type.filter', FilterType::class)
        ->tag('kreyu_data_table.filter.type')
    ;

    $services
        ->set('kreyu_data_table.filter.type.search', SearchFilterType::class)
        ->tag('kreyu_data_table.filter.type')
    ;

    $services
        ->set('kreyu_data_table.filter.type.callback', CallbackFilterType::class)
        ->tag('kreyu_data_table.filter.type')
    ;

    $services
        ->set('kreyu_data_table.filter.filter_clear_url_generator', FilterClearUrlGenerator::class)
        ->args([
            service('request_stack'),
            service(UrlGeneratorInterface::class),
        ])
        ->alias(FilterClearUrlGeneratorInterface::class, 'kreyu_data_table.filter.filter_clear_url_generator')
    ;

    // Doctrine ORM

    $services
        ->set('kreyu_data_table.filter.type.doctrine_orm', DoctrineOrmFilterType::class)
        ->tag('kreyu_data_table.filter.type')
    ;

    $services
        ->set('kreyu_data_table.filter.type.doctrine_orm_string', StringFilterType::class)
        ->tag('kreyu_data_table.filter.type')
    ;

    $services
        ->set('kreyu_data_table.filter.type.doctrine_orm_numeric', NumericFilterType::class)
        ->tag('kreyu_data_table.filter.type')
    ;

    $services
        ->set('kreyu_data_table.filter.type.doctrine_orm_entity', EntityFilterType::class)
        ->args([service('doctrine')])
        ->tag('kreyu_data_table.filter.type')
    ;

    $services
        ->set('kreyu_data_table.filter.type.doctrine_orm_callback', DoctrineOrmCallbackFilterType::class)
        ->tag('kreyu_data_table.filter.type')
    ;

    $services
        ->set('kreyu_data_table.filter.type.doctrine_orm_date', DateFilterType::class)
        ->tag('kreyu_data_table.filter.type')
    ;

    $services
        ->set('kreyu_data_table.filter.type.doctrine_orm_datetime', DateTimeFilterType::class)
        ->tag('kreyu_data_table.filter.type')
    ;

    $services
        ->set('kreyu_data_table.filter.type.doctrine_orm_date_range', DateRangeFilterType::class)
        ->tag('kreyu_data_table.filter.type')
    ;

    $services
        ->set('kreyu_data_table.filter.type.doctrine_orm_boolean', BooleanFilterType::class)
        ->tag('kreyu_data_table.filter.type')
    ;
};
