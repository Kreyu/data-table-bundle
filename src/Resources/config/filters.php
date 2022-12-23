<?php

declare(strict_types=1);

use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\CallbackFilter;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\EntityFilter;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\NumericFilter;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\StringFilter;
use Kreyu\Bundle\DataTableBundle\Filter\Factory\FilterFactory;
use Kreyu\Bundle\DataTableBundle\Filter\Factory\FilterFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterChain;
use Kreyu\Bundle\DataTableBundle\Filter\Mapper\Factory\FilterMapperFactory;
use Kreyu\Bundle\DataTableBundle\Filter\Mapper\Factory\FilterMapperFactoryInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $configurator) {
    $services = $configurator->services();

    $services
        ->set('kreyu_data_table.filter.factory', FilterFactory::class)
        ->args([service('kreyu_data_table.filter.chain')])
        ->alias(FilterFactoryInterface::class, 'kreyu_data_table.filter.factory');

    $services
        ->set('kreyu_data_table.filter.mapper.factory', FilterMapperFactory::class)
        ->args([service('kreyu_data_table.filter.factory')])
        ->alias(FilterMapperFactoryInterface::class, 'kreyu_data_table.filter.mapper.factory');

    $services
        ->set('kreyu_data_table.filter.chain', FilterChain::class)
        ->args([tagged_iterator('kreyu_data_table.filter')]);

    $services
        ->set('kreyu_data_table.filter.doctrine.orm.callback', CallbackFilter::class)
        ->tag('kreyu_data_table.filter');

    $services
        ->set('kreyu_data_table.filter.doctrine.orm.entity', EntityFilter::class)
        ->tag('kreyu_data_table.filter');

    $services
        ->set('kreyu_data_table.filter.doctrine.orm.numeric', NumericFilter::class)
        ->tag('kreyu_data_table.filter');

    $services
        ->set('kreyu_data_table.filter.doctrine.orm.string', StringFilter::class)
        ->tag('kreyu_data_table.filter');
};
