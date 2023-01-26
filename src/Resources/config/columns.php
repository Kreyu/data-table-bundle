<?php

declare(strict_types=1);

use Kreyu\Bundle\DataTableBundle\Column\ColumnFactory;
use Kreyu\Bundle\DataTableBundle\Column\ColumnFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnRegistry;
use Kreyu\Bundle\DataTableBundle\Column\ColumnRegistryInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\ActionsType;
use Kreyu\Bundle\DataTableBundle\Column\Type\BooleanType;
use Kreyu\Bundle\DataTableBundle\Column\Type\CollectionType;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\LinkType;
use Kreyu\Bundle\DataTableBundle\Column\Type\NumberType;
use Kreyu\Bundle\DataTableBundle\Column\Type\ResolvedColumnTypeFactory;
use Kreyu\Bundle\DataTableBundle\Column\Type\ResolvedColumnTypeFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\TemplateType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextType;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $configurator) {
    $services = $configurator->services();

    $services
        ->set('kreyu_data_table.column.resolved_type_factory', ResolvedColumnTypeFactory::class)
        ->alias(ResolvedColumnTypeFactoryInterface::class, 'kreyu_data_table.resolved_type_factory')
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
        ->tag('kreyu_data_table.column.type')
    ;

    $services
        ->set('kreyu_data_table.column.type.actions', ActionsType::class)
        ->tag('kreyu_data_table.column.type')
    ;

    $services
        ->set('kreyu_data_table.column.type.boolean', BooleanType::class)
        ->tag('kreyu_data_table.column.type')
    ;

    $services
        ->set('kreyu_data_table.column.type.collection', CollectionType::class)
        ->tag('kreyu_data_table.column.type')
        ->call('setColumnFactory', [service('kreyu_data_table.column.factory')])
    ;

    $services
        ->set('kreyu_data_table.column.type.link', LinkType::class)
        ->tag('kreyu_data_table.column.type')
    ;

    $services
        ->set('kreyu_data_table.column.type.number', NumberType::class)
        ->tag('kreyu_data_table.column.type')
    ;

    $services
        ->set('kreyu_data_table.column.type.template', TemplateType::class)
        ->tag('kreyu_data_table.column.type')
    ;

    $services
        ->set('kreyu_data_table.column.type.text', TextType::class)
        ->tag('kreyu_data_table.column.type')
    ;
};
