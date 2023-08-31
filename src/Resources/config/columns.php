<?php

declare(strict_types=1);

use Kreyu\Bundle\DataTableBundle\Column\ColumnFactory;
use Kreyu\Bundle\DataTableBundle\Column\ColumnFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnRegistry;
use Kreyu\Bundle\DataTableBundle\Column\ColumnRegistryInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\ActionsColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\BooleanColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\CheckboxColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\CollectionColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\DatePeriodColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\DateTimeColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\FormColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\LinkColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\MoneyColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\NumberColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\ResolvedColumnTypeFactory;
use Kreyu\Bundle\DataTableBundle\Column\Type\ResolvedColumnTypeFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\TemplateColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $configurator) {
    $services = $configurator->services();

    $services
        ->set('kreyu_data_table.column.resolved_type_factory', ResolvedColumnTypeFactory::class)
        ->alias(ResolvedColumnTypeFactoryInterface::class, 'kreyu_data_table.column.resolved_type_factory')
    ;

    $services
        ->set('kreyu_data_table.column.registry', ColumnRegistry::class)
        ->args([
            tagged_iterator('kreyu_data_table.column.type'),
            tagged_iterator('kreyu_data_table.column.type_extension'),
            service('kreyu_data_table.column.resolved_type_factory'),
        ])
        ->alias(ColumnRegistryInterface::class, 'kreyu_data_table.column.registry')
    ;

    $services
        ->set('kreyu_data_table.column.factory', ColumnFactory::class)
        ->args([service('kreyu_data_table.column.registry')])
        ->alias(ColumnFactoryInterface::class, 'kreyu_data_table.column.factory')
    ;

    $services
        ->set('kreyu_data_table.column.type.column', ColumnType::class)
        ->args([service('translator')->nullOnInvalid()])
        ->tag('kreyu_data_table.column.type')
    ;

    $services
        ->set('kreyu_data_table.column.type.actions', ActionsColumnType::class)
        ->tag('kreyu_data_table.column.type')
        ->args([
            service('kreyu_data_table.action.factory'),
        ])
    ;

    $services
        ->set('kreyu_data_table.column.type.checkbox', CheckboxColumnType::class)
        ->tag('kreyu_data_table.column.type')
    ;

    $services
        ->set('kreyu_data_table.column.type.boolean', BooleanColumnType::class)
        ->tag('kreyu_data_table.column.type')
    ;

    $services
        ->set('kreyu_data_table.column.type.collection', CollectionColumnType::class)
        ->tag('kreyu_data_table.column.type')
        ->call('setColumnFactory', [service('kreyu_data_table.column.factory')])
    ;

    $services
        ->set('kreyu_data_table.column.type.form', FormColumnType::class)
        ->tag('kreyu_data_table.column.type')
    ;

    $services
        ->set('kreyu_data_table.column.type.link', LinkColumnType::class)
        ->tag('kreyu_data_table.column.type')
    ;

    $services
        ->set('kreyu_data_table.column.type.number', NumberColumnType::class)
        ->tag('kreyu_data_table.column.type')
    ;

    $services
        ->set('kreyu_data_table.column.type.money', MoneyColumnType::class)
        ->tag('kreyu_data_table.column.type')
    ;

    $services
        ->set('kreyu_data_table.column.type.template', TemplateColumnType::class)
        ->tag('kreyu_data_table.column.type')
    ;

    $services
        ->set('kreyu_data_table.column.type.date_time', DateTimeColumnType::class)
        ->tag('kreyu_data_table.column.type')
    ;

    $services
        ->set('kreyu_data_table.column.type.date_period', DatePeriodColumnType::class)
        ->tag('kreyu_data_table.column.type')
    ;

    $services
        ->set('kreyu_data_table.column.type.text', TextColumnType::class)
        ->tag('kreyu_data_table.column.type')
    ;
};
