<?php

declare(strict_types=1);

use Kreyu\Bundle\DataTableBundle\Column\Factory\ColumnFactory;
use Kreyu\Bundle\DataTableBundle\Column\Factory\ColumnFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Column\Mapper\Factory\ColumnMapperFactory;
use Kreyu\Bundle\DataTableBundle\Column\Mapper\Factory\ColumnMapperFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Column\Renderer\ColumnRendererInterface;
use Kreyu\Bundle\DataTableBundle\Column\Renderer\HtmlColumnRenderer;
use Kreyu\Bundle\DataTableBundle\Column\Type\ActionsType;
use Kreyu\Bundle\DataTableBundle\Column\Type\BooleanType;
use Kreyu\Bundle\DataTableBundle\Column\Type\CollectionType;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnTypeChain;
use Kreyu\Bundle\DataTableBundle\Column\Type\LinkType;
use Kreyu\Bundle\DataTableBundle\Column\Type\NumberType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TemplateType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextType;
use Kreyu\Bundle\DataTableBundle\Column\View\Factory\ColumnHeaderViewFactory;
use Kreyu\Bundle\DataTableBundle\Column\View\Factory\ColumnHeaderViewFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Column\View\Factory\ColumnValueViewFactory;
use Kreyu\Bundle\DataTableBundle\Column\View\Factory\ColumnValueViewFactoryInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $configurator) {
    $services = $configurator->services();

    $services
        ->set('kreyu_data_table.column.factory', ColumnFactory::class)
        ->args([service('kreyu_data_table.column.type_chain')])
        ->alias(ColumnFactoryInterface::class, 'kreyu_data_table.column.factory');

    $services
        ->set('kreyu_data_table.column.mapper.factory', ColumnMapperFactory::class)
        ->args([service('kreyu_data_table.column.factory')])
        ->alias(ColumnMapperFactoryInterface::class, 'kreyu_data_table.column.mapper.factory');

    $services
        ->set('kreyu_data_table.column.renderer.html', HtmlColumnRenderer::class)
        ->arg('$columnHeaderViewFactory', service('kreyu_data_table.column.view.factory.column_header'))
        ->arg('$columnValueViewFactory', service('kreyu_data_table.column.view.factory.column_value'))
        ->arg('$twig', service('twig'))
        ->alias(ColumnRendererInterface::class, 'kreyu_data_table.column.renderer.html');

    $services
        ->set('kreyu_data_table.column.view.factory.column_header', ColumnHeaderViewFactory::class)
        ->alias(ColumnHeaderViewFactoryInterface::class, 'kreyu_data_table.column.view.factory.column_header');

    $services
        ->set('kreyu_data_table.column.view.factory.column_value', ColumnValueViewFactory::class)
        ->alias(ColumnValueViewFactoryInterface::class, 'kreyu_data_table.column.view.factory.column_value');

    $services
        ->set('kreyu_data_table.column.type_chain', ColumnTypeChain::class)
        ->args([tagged_iterator('kreyu_data_table.column_type')]);

    $services
        ->set('kreyu_data_table.column.type.actions', ActionsType::class)
        ->tag('kreyu_data_table.column_type');

    $services
        ->set('kreyu_data_table.column.type.boolean', BooleanType::class)
        ->tag('kreyu_data_table.column_type');

    $services
        ->set('kreyu_data_table.column.type.collection', CollectionType::class)
        ->tag('kreyu_data_table.column_type');

    $services
        ->set('kreyu_data_table.column.type.link', LinkType::class)
        ->tag('kreyu_data_table.column_type');

    $services
        ->set('kreyu_data_table.column.type.number', NumberType::class)
        ->tag('kreyu_data_table.column_type');

    $services
        ->set('kreyu_data_table.column.type.template', TemplateType::class)
        ->tag('kreyu_data_table.column_type');

    $services
        ->set('kreyu_data_table.column.type.text', TextType::class)
        ->tag('kreyu_data_table.column_type');
};
